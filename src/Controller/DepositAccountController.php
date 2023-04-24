<?php

namespace App\Controller;

use App\Dto\AvatarDto;
use App\Enum\AlertEnum;
use App\Service\DepositAccountService;
use App\Service\ProfileService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/deposit-accounts', name: 'deposit_accounts_')]
class DepositAccountController extends AbstractController
{
    #[Route(path: '/select-favorite', name: 'select_favorite', methods: ['POST'])]
    public function selectFavorite(
        Request $request,
        DepositAccountService $depositAccountService,
    ): RedirectResponse {
        $depositAccountId = (int) $request->request->get('favoriteDepositAccount');
        try {
            $depositAccountService->updateFavorite($depositAccountId);
        } catch (\Exception $exception) {
            $this->addAlert(AlertEnum::ERROR, $exception->getMessage());
        }

        return $this->redirectBack('home');
    }
}
