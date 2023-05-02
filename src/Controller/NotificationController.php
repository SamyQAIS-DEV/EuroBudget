<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route(path: '/profil/notifications', name: 'user_notifications', methods: ['GET'])]
    public function index(NotificationService $notificationService, NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        $notificationService->readAll($user);
        $notifications = $notificationRepository->findFor($user);

        return $this->render('profile/notifications.html.twig', [
            'notifications' => $notifications,
            'menu' => 'profile',
        ]);
    }
}
