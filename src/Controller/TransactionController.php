<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    public function __construct(private readonly TransactionRepository $repository)
    {
    }

    #[Route(path: '/profil/factures', name: 'user_transactions', methods: ['GET'])]
    public function index(): Response
    {
        $transactions = $this->repository->findfor($this->getUser());

        return $this->render('profile/transactions.html.twig', [
            'transactions' => $transactions,
            'menu' => 'account',
        ]);
    }

    #[Route(path: '/profil/factures/{id<\d+>}', name: 'user_transaction')]
    public function show(int $id): Response
    {
        $transaction = $this->repository->findOneBy([
            'id' => $id,
            'author' => $this->getUser(),
        ]);

        if (null === $transaction) {
            throw new NotFoundHttpException();
        }

        return $this->render('profile/transaction.html.twig', [
            'transaction' => $transaction,
        ]);
    }
}
