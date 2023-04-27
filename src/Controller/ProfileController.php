<?php

namespace App\Controller;

use App\Repository\DepositAccountRepository;
use App\Repository\OperationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly DepositAccountRepository $depositAccountRepository,
        private readonly OperationRepository $operationRepository,
    ) {
    }

    #[Route(path: '/profil', name: 'user_profile', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('profile/index.html.twig', [
            'depositAccountCount' => $this->depositAccountRepository->countFor($user),
            'operationCount' => $this->operationRepository->countFor($user),
            'menu' => 'profile'
        ]);
    }
}
