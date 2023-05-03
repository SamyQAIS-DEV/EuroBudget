<?php

namespace App\Controller\Template;

use App\Controller\AbstractController;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;

class NotificationStateController extends AbstractController
{
    public function state(NotificationRepository $repository): Response
    {
        $user = $this->getUserOrThrow();
        $notificationsCount = $repository->countUnreadFor($user);

        return $this->render('partials/_bullet.html.twig', [
            'count' => $notificationsCount,
        ]);
    }
}
