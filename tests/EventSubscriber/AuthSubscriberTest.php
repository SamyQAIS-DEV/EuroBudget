<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\Event\LoginLinkRequestedEvent;
use App\EventSubscriber\AuthSubscriber;
use App\Service\LoginLinkService;
use App\Service\MailerService;
use App\Tests\EventSubscriberTest;

class AuthSubscriberTest extends EventSubscriberTest
{
    private LoginLinkService $loginLinkService;

    public function setUp(): void
    {
        parent::setUp();
        $this->loginLinkService = self::getContainer()->get(LoginLinkService::class);
    }

    public function testEventSubscription(): void
    {
        $this->assertArrayHasKey(LoginLinkRequestedEvent::class, AuthSubscriber::getSubscribedEvents());
    }

    public function testSendEmail(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtureFiles(['users']);
        $mailer = self::getContainer()->get(MailerService::class);
        $subscriber = new AuthSubscriber($mailer, $this->loginLinkService);
        $event = new LoginLinkRequestedEvent($user);
        $this->dispatch($subscriber, $event);

        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }

    public function testSendEmailAlreadyExistingToken(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtureFiles(['users', 'login-link-tokens']);
        $mailer = self::getContainer()->get(MailerService::class);
        $subscriber = new AuthSubscriber($mailer, $this->loginLinkService);
        $event = new LoginLinkRequestedEvent($user);
        $this->dispatch($subscriber, $event);

        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }
}