<?php

namespace App\Controller\Api;

use App\Repository\DepositAccountRepository;
use App\Service\DepositAccountService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/deposit-accounts', name: 'deposit_accounts_')]
class DepositAccountController extends AbstractController
{
    public function __construct(
        private readonly DepositAccountService $depositAccountService,
        private readonly DepositAccountRepository $depositAccountRepository,
    ) {
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $depositAccounts = $this->depositAccountRepository->findFor($this->getUser());

        return $this->json(data: $depositAccounts, context: ['groups' => ['read']]);
    }

    #[Route(path: '/favorite-recap', name: 'favorite_recap', methods: ['GET'])]
    public function favoriteRecap(): JsonResponse
    {
        try {
            $resource = $this->depositAccountService->getRecap();

            return $this->json(data: $resource, context: ['groups' => ['read']]);
        } catch (Throwable $e) {
            return $this->json(['title' => 'Erreur lors de la récupération', 'detail' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}