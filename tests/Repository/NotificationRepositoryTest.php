<?php

namespace App\Tests\Repository;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Tests\RepositoryTestCase;

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
    
    public function testFindRecentFor(): void
    {
        /** @var User $user */
        $user = $this->data['user1'];
        $notifications = $this->repository->findFor($user);
        $this->assertCount(1, $notifications);
        $this->assertInstanceOf(Notification::class, $notifications[0]);
    }
}
