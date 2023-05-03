<?php

namespace App\Controller;

use App\Dto\ShareDepositAccountDto;
use App\Entity\DepositAccount;
use App\Entity\User;
use App\Enum\AlertEnum;
use App\Form\DepositAccountFormType;
use App\Form\ShareDepositAccountFormType;
use App\Security\Voter\DepositAccountVoter;
use App\Service\DepositAccountService;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/deposit-accounts', name: 'deposit_accounts_')]
class DepositAccountController extends AbstractController
{
    public function __construct(
        private readonly DepositAccountService $depositAccountService,
        private readonly NotificationService $notificationService,
    ) {
    }

    #[Route(path: '/select-favorite', name: 'select_favorite', methods: ['POST'])]
    public function selectFavorite(Request $request): RedirectResponse
    {
        $depositAccountId = (int) $request->request->get('favoriteDepositAccount');
        try {
            $this->depositAccountService->updateFavorite($depositAccountId);
        } catch (\Exception $exception) {
            $this->addAlert(AlertEnum::ERROR, $exception->getMessage());
        }

        return $this->redirectBack('home');
    }

    #[Route(path: '/{id}/partager', name: 'share', methods: ['GET', 'POST'])]
    public function share(Request $request, User $user): Response
    {
        $shareDepositAccount = new ShareDepositAccountDto($user);
        $form = $this->createForm(ShareDepositAccountFormType::class, $shareDepositAccount);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->depositAccountService->share($shareDepositAccount);
            $this->notificationService->notifyUser($shareDepositAccount->user, sprintf(
                '%s a partagé un compte ("%s") en banque avec vous',
                $this->getUser()->getFullName(),
                $shareDepositAccount->depositAccount->getTitle()
            ));
            $this->addAlert(AlertEnum::SUCCESS, sprintf(
                    'Compte en banque "%s" partagé avec %s',
                    $shareDepositAccount->depositAccount->getTitle(),
                    $shareDepositAccount->user->getFullName()
            ));

            return $this->redirectToRoute('user_profile', ['id' => $shareDepositAccount->user->getId()]);
        }

        return $this->render('deposit-account/share.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $depositAccount = new DepositAccount();
        $form = $this->createForm(DepositAccountFormType::class, $depositAccount);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->depositAccountService->create($depositAccount, $this->getUser());
            $this->addAlert(AlertEnum::SUCCESS, sprintf('Compte en banque "%s" créé', $depositAccount->getTitle()));

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
            $this->addAlert(AlertEnum::ERROR, 'Vous ne pouvez pas modifier ce compte en banque.');

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
