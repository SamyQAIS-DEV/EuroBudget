<?php

namespace App\Service;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Exception\UnauthenticatedException;
use App\Repository\DepositAccountRepository;
use App\Repository\OperationRepository;
use App\Resource\DepositAccountResource;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DepositAccountService
{
    public function __construct(
        private readonly DepositAccountRepository $depositAccountRepository,
        private readonly OperationRepository $operationRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function updateFavorite(int $id): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new UnauthenticatedException();
        }
        $depositAccount = $this->depositAccountRepository->find($id);
        if (!$depositAccount instanceof DepositAccount) {
            throw new EntityNotFoundException();
        }
        if (!$depositAccount->getUsers()->contains($user)) {
            throw new AccessDeniedException();
        }
        $user->setFavoriteDepositAccount($depositAccount);
        $user->setUpdatedAt(new DateTimeImmutable());
        $this->entityManager->flush();
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
        $operationsRecap = $this->operationRepository->findForRecap($favoriteDepositAccount);

        return DepositAccountResource::fromDepositAccount(
            $favoriteDepositAccount,
            $operationsRecap['waitingOperationsNb'],
            $operationsRecap['waitingAmount']
        );
    }
}