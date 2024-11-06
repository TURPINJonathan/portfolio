<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(UserInterface $user): JsonResponse
    {
        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}
