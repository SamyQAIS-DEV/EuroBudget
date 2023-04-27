<?php

namespace App\Controller;

use App\Entity\DepositAccount;
use App\Enum\AlertEnum;
use App\Form\DepositAccountFormType;
use App\Security\Voter\DepositAccountVoter;
use App\Service\DepositAccountService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/deposit-accounts', name: 'deposit_accounts_')]
class DepositAccountController extends AbstractController
{
    public function __construct(
        private readonly DepositAccountService $depositAccountService,
    ) {
    }

    #[Route(path: '/select-favorite', name: 'select_favorite', methods: ['POST'])]
    public function selectFavorite(Request $request): RedirectResponse {
        $depositAccountId = (int) $request->request->get('favoriteDepositAccount');
        try {
            $this->depositAccountService->updateFavorite($depositAccountId);
        } catch (\Exception $exception) {
            $this->addAlert(AlertEnum::ERROR, $exception->getMessage());
        }

        return $this->redirectBack('home');
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $depositAccount = new DepositAccount();
        $form = $this->createForm(DepositAccountFormType::class, $depositAccount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->depositAccountService->create($depositAccount, $this->getUser());

            return $this->redirectToRoute('home');
        }

        return $this->render('deposit-account/new.html.twig', [
            'depositAccount' => $depositAccount,
            'form' => $form->createView(),
            'menu' => 'deposit-accounts',
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DepositAccount $depositAccount): Response
    {
        if (!$this->isGranted(DepositAccountVoter::UPDATE, $depositAccount)) {
            $this->addAlert(AlertEnum::ERROR, 'Vous ne pouvez pas modifier cette facture.');

            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(DepositAccountFormType::class, $depositAccount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->depositAccountService->update($depositAccount);

            return $this->redirectToRoute('home');
        }

        return $this->render('deposit-account/edit.html.twig', [
            'depositAccount' => $depositAccount,
            'form' => $form->createView(),
            'menu' => 'deposit-accounts',
        ]);
    }
}
