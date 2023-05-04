<?php

namespace App\Tests\Repository;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Tests\RepositoryTestCase;
use DateTimeImmutable;

/**
 * @property NotificationRepository $repository
 */
class NotificationRepositoryTest extends RepositoryTestCase
{
    protected $repositoryClass = NotificationRepository::class;

    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->loadFixtures(['users', 'notifications']);
    }

    public function testFindFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $notifications = $this->repository->findFor($user);
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(Notification::class, $notifications[0]);
    }
    
    public function testCountUnreadFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $user->setNotificationsReadAt(new DateTimeImmutable('-1 day'));
        $this->repository->flush();
        $notificationsCount = $this->repository->countUnreadFor($user);
        $this->assertSame(1, $notificationsCount);

        $user->setNotificationsReadAt(new DateTimeImmutable('+1 day'));
        $this->repository->flush();
        $notificationsCount = $this->repository->countUnreadFor($user);
        $this->assertSame(0, $notificationsCount);
    }
}
