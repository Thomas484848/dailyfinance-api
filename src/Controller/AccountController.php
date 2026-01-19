<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/me/password', name: 'api_me_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->toArray();
        $currentPassword = isset($payload['currentPassword']) ? (string) $payload['currentPassword'] : '';
        $newPassword = isset($payload['newPassword']) ? (string) $payload['newPassword'] : '';

        if ($currentPassword === '' || $newPassword === '') {
            return $this->json(['message' => 'currentPassword and newPassword are required.'], 400);
        }

        if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json(['message' => 'Invalid current password.'], 400);
        }

        $user->setPasswordHash($passwordHasher->hashPassword($user, $newPassword));
        $this->entityManager->flush();

        return $this->json(['message' => 'Password updated.']);
    }
}
