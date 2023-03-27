<?php

namespace App\Controller\Api;

use App\Repository\OperationRepository;
use App\Resource\DepositAccountResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/deposit-accounts', name: 'deposit_accounts_')]
class DepositAccountController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository
    ) {
    }

    #[Route(path: '/favorite-recap', name: 'favorite_recap', methods: ['GET'])]
    public function favoriteRecap(): JsonResponse
    {
        try {
            $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
            $operationsRecap = $this->operationRepository->findForRecap($favoriteDepositAccount->getId());
            $resource = DepositAccountResource::fromDepositAccount(
                $favoriteDepositAccount,
                $operationsRecap['waitingOperationsNb'],
                $operationsRecap['waitingAmount']
            );

            return $this->json(data: $resource, context: ['groups' => ['read']]);
        } catch (Throwable $e) {
            return $this->json(['title' => 'Erreur lors de la récupération', 'detail' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}