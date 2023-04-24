<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/invoices', name: 'invoice_')]
class InvoiceController extends AbstractController
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $invoices = $this->invoiceRepository->findByDepositAccount($favoriteDepositAccount->getId());

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices
        ]);
    }
}
