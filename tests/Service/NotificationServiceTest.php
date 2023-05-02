<?php

namespace App\Tests\Service;

use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use App\Tests\KernelTestCase;
use DateTimeImmutable;

class NotificationServiceTest extends KernelTestCase
{
    private NotificationRepository $notificationRepository;
    private UserRepository $userRepository;
    private NotificationService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->notificationRepository = self::getContainer()->get(NotificationRepository::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->service = self::getContainer()->get(NotificationService::class);
    }

    public function testNotifyUser(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $notification = $this->service->notifyUser($user, 'Bonjour');
        $this->assertEquals($user->getId(), $notification->getUser()->getId());
    }

    public function testReadAll(): void
    {
        ['user1' => $user] = $this->loadFixtures(['users']);
        $previousDate = new DateTimeImmutable('- 10 days');
        $user->setNotificationsReadAt($previousDate);
        $this->service->readAll($user);
        $this->assertNotEquals($previousDate, $user->getNotificationsReadAt());
    }
}
