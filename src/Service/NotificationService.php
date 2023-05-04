<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    public function notifyUser(User $user, string $message, string $url = null): Notification
    {
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setUser($user);
        $this->notificationRepository->save($notification, true);

        return $notification;
    }

    public function readAll(User $user): void
    {
        $user->setNotificationsReadAt(new DateTimeImmutable());
        $this->notificationRepository->flush();
    }
}
