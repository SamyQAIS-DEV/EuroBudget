<?php

namespace App\Controller;

use App\Dto\DepositAccountShareRequestDto;
use App\Entity\DepositAccount;
use App\Entity\User;
use App\Enum\AlertEnum;
use App\Event\DepositAccountShareRequestCreatedEvent;
use App\Form\DepositAccountFormType;
use App\Form\DepositAccountShareRequestFormType;
use App\Security\Voter\DepositAccountVoter;
use App\Service\DepositAccountService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/deposit-accounts', name: 'deposit_accounts_')]
class DepositAccountController extends AbstractController
{
    public function __construct(
        private readonly DepositAccountService $depositAccountService,
        private readonly EventDispatcherInterface $dispatcher,
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
        $depositAccountShareRequest = new DepositAccountShareRequestDto($user);
        $form = $this->createForm(DepositAccountShareRequestFormType::class, $depositAccountShareRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->dispatcher->dispatch(new DepositAccountShareRequestCreatedEvent($depositAccountShareRequest));

            $this->addAlert(AlertEnum::SUCCESS, sprintf(
                    'Demande de partage de "%s" envoyé à %s',
                    $depositAccountShareRequest->depositAccount->getTitle(),
                    $depositAccountShareRequest->user->getFullName()
            ));

            return $this->redirectToRoute('user_profile', ['id' => $depositAccountShareRequest->user->getId()]);
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
