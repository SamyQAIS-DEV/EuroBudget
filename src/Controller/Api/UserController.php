<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (empty($q)) {
            return $this->json([]);
        }

        if (strtolower($q) === 'moi') {
            return $this->json(data: [$this->getUser()], context: ['groups' => ['read']]);
        }

        $users = $this->userRepository->search($q);

        return $this->json(data: $users, context: ['groups' => ['read']]);
    }
}