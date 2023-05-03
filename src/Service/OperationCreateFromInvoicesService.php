<?php

namespace App\Service;

use App\Entity\Operation;
use App\Entity\User;
use App\Enum\TypeEnum;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;

class OperationCreateFromInvoicesService
{
    public function __construct(private readonly InvoiceRepository $invoiceRepository)
    {
    }

    public function process(User $user): int
    {
        $favoriteDepositAccount = $user->getFavoriteDepositAccount();
        $invoices = $this->invoiceRepository->findByDepositAccount($favoriteDepositAccount, true);

        foreach ($invoices as $invoice) {
            $operation = new Operation();
            $operation
                ->setLabel($invoice->getLabel())
                ->setAmount($invoice->getAmount())
                ->setType(TypeEnum::DEBIT)
                ->setDate(new DateTimeImmutable())
                ->setPast(false)
                ->setInvoice($invoice)
                ->setCreator($user)
                ->setDepositAccount($user->getFavoriteDepositAccount());

            if ($user->isPremium()) {
                $operation->setCategory($invoice->getCategory());
            }

            $this->invoiceRepository->save($operation);
        }

        $this->invoiceRepository->flush();

        return count($invoices);
    }
}