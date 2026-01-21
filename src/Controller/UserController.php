<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class UserController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Unauthorized');
        }

        if ($request->getMethod() === 'PATCH') {
            $payload = $request->toArray();

            if (array_key_exists('avatarUrl', $payload)) {
                $avatarUrl = $payload['avatarUrl'];
                if ($avatarUrl !== null && !is_string($avatarUrl)) {
                    throw new \InvalidArgumentException('avatarUrl must be a string or null.');
                }
                $avatarValue = is_string($avatarUrl) ? trim($avatarUrl) : null;
                if ($avatarValue === '') {
                    $avatarValue = null;
                }
                $user->setAvatarUrl($avatarValue);
            }

            if (array_key_exists('firstName', $payload)) {
                $firstName = $payload['firstName'];
                if ($firstName !== null && !is_string($firstName)) {
                    throw new \InvalidArgumentException('firstName must be a string or null.');
                }
                $user->setFirstName($firstName !== null ? trim($firstName) : null);
            }

            if (array_key_exists('lastName', $payload)) {
                $lastName = $payload['lastName'];
                if ($lastName !== null && !is_string($lastName)) {
                    throw new \InvalidArgumentException('lastName must be a string or null.');
                }
                $user->setLastName($lastName !== null ? trim($lastName) : null);
            }

            $this->entityManager->flush();
        }

        return $this->json($this->serializeUser($user));
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'avatarUrl' => $user->getAvatarUrl(),
            'createdAt' => $user->getCreatedAt()?->format('c'),
            'updatedAt' => $user->getUpdatedAt()?->format('c'),
        ];
    }
}
