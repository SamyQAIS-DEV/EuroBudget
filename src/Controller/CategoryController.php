<?php

namespace App\Controller;

use App\Entity\Category;
use App\Enum\AlertEnum;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use App\Security\Voter\CategoryVoter;
use App\Service\CategoryService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/categories', name: 'categories_')]
#[IsGranted(CategoryVoter::ACCESS)]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $categories = $categoryRepository->findByDepositAccount($favoriteDepositAccount);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'menu' => 'categories',
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->create($category, $this->getUser());

            return $this->redirectToRoute('categories_index');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'menu' => 'categories',
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        if (!$this->isGranted(CategoryVoter::UPDATE, $category)) {
            $this->addAlert(AlertEnum::ERROR, 'Vous ne pouvez pas modifier cette catégorie.');

            return $this->redirectToRoute('categories_index');
        }
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->update($category);

            return $this->redirectToRoute('categories_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'menu' => 'categories',
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        if (!$this->isGranted(CategoryVoter::DELETE, $category)) {
            $this->addAlert(AlertEnum::ERROR, 'Vous ne pouvez pas supprimer cette catégorie.');

            return $this->redirectToRoute('categories_index');
        }
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->categoryService->delete($category);
        }

        return $this->redirectToRoute('categories_index');
    }
}
