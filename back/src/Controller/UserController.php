<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'app_user_list', methods: ['GET'])]
    public function list(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $users = $userRepository->findAll();
        $data  = $serializer->serialize($users, 'json', ['groups' => 'user:read']);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/create', name: 'app_user_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $content = $request->getContent();
        if (!is_string($content)) {
            return $this->json([
                'errors' => 'Invalid request content',
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return $this->json([
                'errors' => 'Invalid JSON data',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['email'], $data['password'], $data['roles'])) {
            return $this->json([
                'errors' => 'Missing required fields',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = new User($data['email'], $data['roles']);

        // Hash the password before setting it
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json([
                'errors' => $errorMessages,
            ], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'User created successfully!',
            'user'    => [
                'id'    => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], Response::HTTP_CREATED);
    }
}
