<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
    ) {
    }

    public function create(Invoice $invoice, User $user): Invoice
    {
        $invoice->setCreator($user);
        $invoice->setDepositAccount($user->getFavoriteDepositAccount());
        $this->invoiceRepository->save($invoice, true);

        return $invoice;
    }

    public function update(Invoice $invoice): Invoice
    {
        $invoice->setUpdatedAt(new DateTimeImmutable());
        $this->invoiceRepository->save($invoice, true);

        return $invoice;
    }

    public function delete(Invoice $invoice): void
    {
        $this->invoiceRepository->remove($invoice, true);
    }
}