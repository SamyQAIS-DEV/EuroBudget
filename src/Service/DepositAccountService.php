<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UnauthenticatedException;
use App\Repository\OperationRepository;
use App\Resource\DepositAccountResource;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;

class DepositAccountService
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly Security $security,
    ) {
    }

    /**
     * @throws NonUniqueResultException|UnauthenticatedException
     */
    public function getRecap(): DepositAccountResource
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new UnauthenticatedException();
        }
        $favoriteDepositAccount = $user->getFavoriteDepositAccount();
        $operationsRecap = $this->operationRepository->findForRecap($favoriteDepositAccount->getId());

        return DepositAccountResource::fromDepositAccount(
            $favoriteDepositAccount,
            $operationsRecap['waitingOperationsNb'],
            $operationsRecap['waitingAmount']
        );
    }
}