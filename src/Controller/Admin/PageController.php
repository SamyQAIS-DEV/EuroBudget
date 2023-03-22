<?php

namespace App\Controller\Admin;

use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('admin/home.html.twig', [
            'months' => $transactionRepository->getMonthlyRevenues(),
            'menu' => 'home',
        ]);
    }
}