<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\DoctrineEncryptSubscriber;
use App\Service\Encryptors\EntityEncryptor;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;

class DoctrineEncryptSubscriberTest extends EventSubscriberTest
{
    /** @var User[] */
    private array $users = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->users = $this->loadFixtureFiles(['users']);
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