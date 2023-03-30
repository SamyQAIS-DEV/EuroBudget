<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\Event\LoginLinkRequestedEvent;
use App\Event\UserCreatedEvent;
use App\EventSubscriber\AuthSubscriber;
use App\Service\LoginLinkService;
use App\Service\MailerService;
use App\Tests\EventSubscriberTest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AuthSubscriberTest extends EventSubscriberTest
{
    private MailerService $mailer;
    private LoginLinkService $loginLinkService;
    private EventDispatcherInterface $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->mailer = self::getContainer()->get(MailerService::class);
        $this->loginLinkService = self::getContainer()->get(LoginLinkService::class);
        $this->dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
    }

    public function testEventSubscription(): void
    {
        $this->assertArrayHasKey(LoginLinkRequestedEvent::class, AuthSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(UserCreatedEvent::class, AuthSubscriber::getSubscribedEvents());
    }

    public function testSendLoginLinkEmail(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $subscriber = new AuthSubscriber($this->mailer, $this->loginLinkService, $this->em, $this->dispatcher);
        $event = new LoginLinkRequestedEvent($user);
        $this->dispatch($subscriber, $event);

        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }

    public function testSendEmailAlreadyExistingLoginLink(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users', 'login-links']);
        $subscriber = new AuthSubscriber($this->mailer, $this->loginLinkService, $this->em, $this->dispatcher);
        $event = new LoginLinkRequestedEvent($user);
        $this->dispatch($subscriber, $event);

        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }

    public function testOnUserCreated(): void
    {
        /** @var User $user */
        ['no_deposit_account_user' => $user] = $this->loadFixtures(['users']);
        $subscriber = new AuthSubscriber($this->mailer, $this->loginLinkService, $this->em, $this->dispatcher);
        $event = new UserCreatedEvent($user);
        $this->dispatch($subscriber, $event);

        self::assertEmailCount(2);
        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Votre inscription !', $email->getSubject());
    }
}