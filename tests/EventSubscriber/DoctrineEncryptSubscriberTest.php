<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\Tests\EventSubscriberTest;

class DoctrineEncryptSubscriberTest extends EventSubscriberTest
{
    /** @var User[] */
    private array $users = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->users = $this->loadFixtures(['users']);
    }

    public function testPostLoad(): void
    {
        $user = $this->users['user1'];
        $userFromRepo = $this->em->getRepository(User::class)->find($user->getId());

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }

    public function testPrePersist(): void
    {
        $this->markTestSkipped();
    }

    public function testPreUpdate(): void
    {
        $this->markTestSkipped();
    }

    public function testPostUpdate(): void
    {
        $this->markTestSkipped();
    }

    public function testPreFlush(): void
    {
        $this->markTestSkipped();
    }

    public function testPostFlush(): void
    {
        $this->markTestSkipped();
    }
}