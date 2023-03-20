<?php

namespace App\Controller;

use App\Security\Voter\CategoryVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    #[IsGranted(CategoryVoter::ACCESS)]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'menu' => 'categories',
        ]);
    }
}
