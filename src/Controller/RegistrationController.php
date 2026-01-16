<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $users,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $payload = $request->toArray();
        $email = isset($payload['email']) ? trim((string) $payload['email']) : '';
        $plainPassword = isset($payload['password']) ? (string) $payload['password'] : '';

        if ($email === '' || $plainPassword === '') {
            return new JsonResponse(['message' => 'Email and password are required.'], 400);
        }

        if ($users->findOneBy(['email' => $email]) !== null) {
            return new JsonResponse(['message' => 'Email already registered.'], 409);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['id' => $user->getId(), 'email' => $user->getEmail()], 201);
    }
}
