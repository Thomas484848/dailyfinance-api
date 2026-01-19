<?php

namespace App\Controller;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class PasswordResetController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/password-reset/request', name: 'api_password_reset_request', methods: ['POST'])]
    public function requestReset(Request $request): JsonResponse
    {
        $payload = $request->toArray();
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        if ($email === '') {
            return $this->json(['message' => 'Email is required.'], 400);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['message' => 'If the account exists, a reset link was sent.']);
        }

        $this->entityManager->createQueryBuilder()
            ->update(PasswordResetToken::class, 't')
            ->set('t.usedAt', ':usedAt')
            ->where('t.user = :user')
            ->andWhere('t.usedAt IS NULL')
            ->setParameter('usedAt', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $resetToken = new PasswordResetToken();
        $resetToken->setUser($user);
        $resetToken->setTokenHash($tokenHash);
        $resetToken->setExpiresAt(new \DateTimeImmutable('+1 hour'));
        $resetToken->setIpAddress($request->getClientIp());
        $resetToken->setUserAgent($request->headers->get('User-Agent'));

        $this->entityManager->persist($resetToken);
        $this->entityManager->flush();

        return $this->json(['message' => 'If the account exists, a reset link was sent.']);
    }

    #[Route('/api/password-reset/confirm', name: 'api_password_reset_confirm', methods: ['POST'])]
    public function confirmReset(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $payload = $request->toArray();
        $token = isset($payload['token']) ? trim((string) $payload['token']) : '';
        $newPassword = isset($payload['password']) ? (string) $payload['password'] : '';

        if ($token === '' || $newPassword === '') {
            return $this->json(['message' => 'Token and password are required.'], 400);
        }

        $tokenHash = hash('sha256', $token);
        $qb = $this->entityManager->createQueryBuilder();
        $resetToken = $qb
            ->select('t')
            ->from(PasswordResetToken::class, 't')
            ->where('t.tokenHash = :tokenHash')
            ->andWhere('t.usedAt IS NULL')
            ->andWhere('t.expiresAt > :now')
            ->setParameter('tokenHash', $tokenHash)
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$resetToken instanceof PasswordResetToken) {
            return $this->json(['message' => 'Invalid or expired token.'], 400);
        }

        $user = $resetToken->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Invalid token.'], 400);
        }

        $user->setPasswordHash($passwordHasher->hashPassword($user, $newPassword));
        $resetToken->setUsedAt(new \DateTimeImmutable());

        $this->entityManager->createQueryBuilder()
            ->update(PasswordResetToken::class, 't')
            ->set('t.usedAt', ':usedAt')
            ->where('t.user = :user')
            ->andWhere('t.usedAt IS NULL')
            ->setParameter('usedAt', new \DateTimeImmutable())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        $this->entityManager->flush();

        return $this->json(['message' => 'Password updated.']);
    }
}
