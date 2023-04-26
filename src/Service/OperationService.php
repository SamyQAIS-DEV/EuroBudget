<?php

namespace App\Service;

use App\Entity\Operation;
use App\Entity\User;
use App\Exception\OperationServiceException;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperationService
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CalculatorService $calculatorService,
        private readonly OperationRepository $operationRepository,
    ) {
    }

    public function create(Operation $operation, User $user): Operation
    {
        $operation->setCreator($user)->setDepositAccount($user->getFavoriteDepositAccount());
        $errors = $this->validator->validate($operation);
        if (count($errors) > 0) {
            throw new OperationServiceException($errors);
        }
        $amount = $this->calculatorService->calculate($operation);
        $operation->getDepositAccount()->setAmount($operation->getDepositAccount()->getAmount() + $amount);
        $this->operationRepository->save($operation, true);

        return $operation;
    }

    public function update(Operation $operation, Operation $originalOperation): Operation
    {
        $errors = $this->validator->validate($operation);
        if (count($errors) > 0) {
            throw new OperationServiceException($errors);
        }
        $amount = $this->calculatorService->calculate($operation, $originalOperation);
        $operation->getDepositAccount()->setAmount($operation->getDepositAccount()->getAmount() + $amount);
        $this->operationRepository->save($operation, true);

        return $operation;
    }

    public function delete(Operation $operation): void
    {
        $amount = $this->calculatorService->calculate(null, $operation);
        $operation->getDepositAccount()->setAmount($operation->getDepositAccount()->getAmount() + $amount);
        $this->operationRepository->remove($operation, true);
    }
}