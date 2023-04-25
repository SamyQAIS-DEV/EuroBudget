<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Security\Voter\CategoryVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/categories', name: 'category_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    #[IsGranted(CategoryVoter::ACCESS)]
    public function index(): Response
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $categories = $this->categoryRepository->findByDepositAccount($favoriteDepositAccount->getId());

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'menu' => 'categories',
        ]);
    }
}
