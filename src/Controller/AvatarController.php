<?php

namespace App\Controller;

use App\Dto\AvatarDto;
use App\Enum\AlertEnum;
use App\Service\ProfileService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AvatarController extends AbstractController
{
    #[Route('/profil/avatar', name: 'user_avatar', methods: ['POST'])]
    public function avatar(
        Request $request,
        ProfileService $profileService,
    ): RedirectResponse {
        $user = $this->getUser();
        $data = new AvatarDto($request->files->get('avatar'), $user);
        try {
            $profileService->updateAvatar($data);
            $this->addAlert(AlertEnum::SUCCESS, 'Avatar mis à jour');
        } catch (\Exception $exception) {
            $this->addAlert(AlertEnum::ERROR, $exception->getMessage());
        }

        return $this->redirectToRoute('user_profile');
    }
}
