<?php

namespace App\Controller;

use App\Entity\User;
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

    #[Route(path: '/profil', name: 'my_profile', methods: ['GET'])]
    public function myProfile(): Response
    {
        $user = $this->getUserOrThrow();

        return $this->render('profile/my_profile.html.twig', [
            'depositAccountCount' => $this->depositAccountRepository->countFor($user),
            'operationCount' => $this->operationRepository->countFor($user),
            'menu' => 'profile'
        ]);
    }

    #[Route(path: '/profil/{id}', name: 'user_profile', methods: ['GET'])]
    public function profile(User $user): Response
    {
        if ($user->getId() === $this->getUserOrThrow()->getId()) {
            return $this->redirectToRoute('my_profile');
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'depositAccountCount' => $this->depositAccountRepository->countFor($user),
            'operationCount' => $this->operationRepository->countFor($user),
        ]);
    }
}
