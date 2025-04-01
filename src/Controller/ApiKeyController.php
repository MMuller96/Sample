<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiKeyController extends AbstractController
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ){}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $login = $data['login'] ?? null;
        $password = $data['password'] ?? null;

        if (!$login || !$password) {
            return new JsonResponse(['error' => 'Missing credentials'], 400);
        }

        $user = $this->userRepository->findOneBy(['login' => $login]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}