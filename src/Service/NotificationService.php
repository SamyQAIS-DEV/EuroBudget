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
        private readonly UserRepository $userRepository,
    ) {
    }

    public function notifyUser(User $user, string $message): Notification
    {
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($message)
            ->setUser($user);
        $this->notificationRepository->save($notification);

        return $notification;
    }

    public function readAll(User $user): void
    {
        $user->setNotificationsReadAt(new DateTimeImmutable());
        $this->userRepository->save($user);
    }
}
