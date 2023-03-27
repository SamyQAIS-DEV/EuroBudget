<?php

namespace App\Controller\Api;

use App\Repository\OperationRepository;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/operations', name: 'operations_')]
class OperationController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operationRepository
    ) {
    }

    #[Route(path: '/current-month', name: 'current_month', methods: ['GET'])]
    public function currentMonth(): JsonResponse
    {
        $now = new DateTime();
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $operations = $this->operationRepository->findForYearAndMonth($favoriteDepositAccount->getId(), (int) $now->format('Y'), (int) $now->format('m'));

        return $this->json(data: $operations, context: ['groups' => ['read']]);
    }

    #[Route(path: '/{year}/{month}', name: 'for_month', methods: ['GET'])]
    public function forMonth(int $year, int $month): JsonResponse
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $operations = $this->operationRepository->findForYearAndMonth($favoriteDepositAccount->getId(), $year, $month);

        return $this->json(data: $operations, context: ['groups' => ['read']]);
    }

    #[Route(path: '/years-months', name: 'years_months', methods: ['GET'])]
    public function yearsMonths(): JsonResponse
    {
        $favoriteDepositAccount = $this->getUser()->getFavoriteDepositAccount();
        $yearsMonths = $this->operationRepository->findYearsMonths($favoriteDepositAccount->getId());

        return $this->json(data: $yearsMonths, context: ['groups' => ['read']]);
    }
}