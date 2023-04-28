<?php

namespace App\Service;

use App\Entity\DepositAccount;
use App\Entity\Category;
use App\Entity\Operation;
use App\Entity\User;
use App\Exception\CalculatorException;
use App\Exception\OperationServiceException;
use App\Repository\CategoryRepository;
use App\Repository\OperationRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OperationService
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CalculatorService $calculatorService,
        private readonly OperationRepository $operationRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @param Operation $operation
     * @param User $user
     * @param DepositAccount $depositAccount
     * @return Operation
     * @throws OperationServiceException
     * @throws CalculatorException
     */
    public function create(Operation $operation, User $user, DepositAccount $depositAccount): Operation
    {
        if ($operation->getCategory() instanceof Category) {
            $operation->setCategory($this->categoryRepository->find($operation->getCategory()->getId()));
        }
        $operation->setCreator($user)->setDepositAccount($depositAccount);
        $errors = $this->validator->validate($operation);
        if (count($errors) > 0) {
            throw new OperationServiceException($errors);
        }
        $amount = $this->calculatorService->calculate($operation);
        $operation->getDepositAccount()->setAmount($operation->getDepositAccount()->getAmount() + $amount);
        $this->operationRepository->save($operation, true);

        return $operation;
    }

    /**
     * @param Operation $operation
     * @param Operation $originalOperation
     * @return Operation
     * @throws CalculatorException
     */
    public function update(Operation $operation, Operation $originalOperation): Operation
    {
        if ($operation->getCategory() instanceof Category) {
            $operation->setCategory($this->categoryRepository->find($operation->getCategory()->getId()));
        }
        $errors = $this->validator->validate($operation);
        if (count($errors) > 0) {
            throw new OperationServiceException($errors);
        }
        $amount = $this->calculatorService->calculate($operation, $originalOperation);
        $operation->getDepositAccount()->setAmount($operation->getDepositAccount()->getAmount() + $amount);
        $this->operationRepository->save($operation, true);

        return $operation;
    }

    /**
     * @param Operation $operation
     * @return void
     * @throws CalculatorException
     */
    public function delete(Operation $operation): void
    {
        $amount = $this->calculatorService->calculate(null, $operation);
        $operation->getDepositAccount()->setAmount($operation->getDepositAccount()->getAmount() + $amount);
        $this->operationRepository->remove($operation, true);
    }
}