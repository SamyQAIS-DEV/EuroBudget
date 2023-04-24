<?php

namespace App\Service;

use App\Entity\Operation;
use App\Entity\User;
use App\Enum\TypeEnum;
use App\Repository\InvoiceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class OperationCreateFormInvoicesService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function process(User $user): int
    {
        $favoriteDepositAccount = $user->getFavoriteDepositAccount();
        $invoices = $this->invoiceRepository->findByDepositAccount($favoriteDepositAccount->getId(), true);
        $count = 0;

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

            $this->entityManager->persist($operation);
            $count++;
        }

        $this->entityManager->flush();

        return $count;
    }
}