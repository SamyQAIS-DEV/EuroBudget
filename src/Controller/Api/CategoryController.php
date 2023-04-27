<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories', name: 'categories_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $favoritDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $categories = $this->categoryRepository->findByDepositAccount($favoritDepositAccount->getId());

        return $this->json(data: $categories, context: ['groups' => ['read']]);
    }
}