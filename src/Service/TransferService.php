<?php

namespace App\Service;

use App\Dto\TransferDto;
use App\Entity\Operation;
use App\Entity\User;
use App\Enum\TypeEnum;
use App\Exception\CalculatorException;
use App\Exception\OperationServiceException;

class TransferService
{
    public function __construct(
        private readonly OperationService $operationService,
    ) {
    }

    /**
     * @param TransferDto $transfer
     * @param User $user
     * @return TransferDto
     * @throws CalculatorException
     * @throws OperationServiceException
     */
    public function create(TransferDto $transfer, User $user): TransferDto
    {
        $operationA = (new Operation())
            ->setLabel(sprintf('Virement vers le %s', $transfer->targetDepositAccount->getTitle()))
            ->setAmount($transfer->amount)
            ->setType(TypeEnum::DEBIT)
            ->setPast(true)
            ->setTransfer(true);
        $operationB = (new Operation())
            ->setLabel(sprintf('Virement depuis le %s (%s)', $transfer->fromDepositAccount->getTitle(), $user->getFullName()))
            ->setAmount($transfer->amount)
            ->setType(TypeEnum::CREDIT)
            ->setPast(false)
            ->setTransfer(true);
        $this->operationService->create($operationA, $user, $transfer->fromDepositAccount);
        $this->operationService->create($operationB, $user, $transfer->targetDepositAccount);

        return $transfer;
    }
}