<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Enum\AlertEnum;
use App\Form\InvoiceFormType;
use App\Repository\InvoiceRepository;
use App\Security\Voter\InvoiceVoter;
use App\Service\InvoiceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/invoices', name: 'invoice_')]
class InvoiceController extends AbstractController
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $invoices = $invoiceRepository->findByDepositAccount($favoriteDepositAccount->getId());

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoices,
            'menu' => 'invoices',
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceFormType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->invoiceService->create($invoice, $this->getUser());

            return $this->redirectToRoute('invoice_index');
        }

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form->createView(),
            'menu' => 'invoices',
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice): Response
    {
        if (!$this->isGranted(InvoiceVoter::UPDATE, $invoice)) {
            $this->addAlert(AlertEnum::ERROR, 'Vous ne pouvez pas modifier cette facture.');

            return $this->redirectToRoute('invoice_index');
        }
        $form = $this->createForm(InvoiceFormType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->invoiceService->update($invoice);

            return $this->redirectToRoute('invoice_index');
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form->createView(),
            'menu' => 'invoices',
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Invoice $invoice): Response
    {
        if (!$this->isGranted(InvoiceVoter::DELETE, $invoice)) {
            $this->addAlert(AlertEnum::ERROR, 'Vous ne pouvez pas supprimer cette facture.');

            return $this->redirectToRoute('invoice_index');
        }
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->request->get('_token'))) {
            $this->invoiceService->delete($invoice);
        }

        return $this->redirectToRoute('invoice_index');
    }
}
