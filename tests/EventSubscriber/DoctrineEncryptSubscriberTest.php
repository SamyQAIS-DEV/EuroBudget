<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\DoctrineEncryptSubscriber;
use App\Service\Encryptors\EntityEncryptor;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\Events;

class DoctrineEncryptSubscriberTest extends EventSubscriberTest
{
    /** @var User[] */
    private array $users = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->users = $this->loadFixtures(['users']);
    }

    public function testEventSubscription(): void
    {
        $entityEncryptor = self::getContainer()->get(EntityEncryptor::class);
        $subscriber = new DoctrineEncryptSubscriber($entityEncryptor);

        $this->assertContains(Events::postLoad, $subscriber->getSubscribedEvents());
        $this->assertContains(Events::prePersist, $subscriber->getSubscribedEvents());
        $this->assertContains(Events::preUpdate, $subscriber->getSubscribedEvents());
        $this->assertContains(Events::postUpdate, $subscriber->getSubscribedEvents());
        $this->assertContains(Events::preFlush, $subscriber->getSubscribedEvents());
        $this->assertContains(Events::postFlush, $subscriber->getSubscribedEvents());
    }

    public function testPostLoad(): void
    {
        $user = $this->users['user1'];
        $userFromRepo = $this->em->getRepository(User::class)->find($user->getId());

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }

    public function testPrePersist(): void
    {
        $user = (new User())->setEmail('prePersist@domain.fr');
        $this->em->persist($user);
        $this->em->flush();
        $userFromRepo = $this->em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }

    public function testPreUpdate(): void
    {
        $user = $this->users['user1'];
        $userFromRepo = $this->em->getRepository(User::class)->find($user->getId());

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }

    public function testPostUpdate(): void
    {
        $user = $this->users['user1'];
        $userFromRepo = $this->em->getRepository(User::class)->find($user->getId());

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }

    public function testPreFlush(): void
    {
        $user = $this->users['user1'];
        $userFromRepo = $this->em->getRepository(User::class)->find($user->getId());

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }

    public function testPostFlush(): void
    {
        $user = $this->users['user1'];
        $userFromRepo = $this->em->getRepository(User::class)->find($user->getId());

        $this->assertSame($userFromRepo->getEmail(), $user->getEmail());
    }
}