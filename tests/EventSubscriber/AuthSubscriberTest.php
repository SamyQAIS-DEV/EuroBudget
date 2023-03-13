<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\Event\LoginLinkRequestedEvent;
use App\EventSubscriber\AuthSubscriber;
use App\Service\MailerService;
use App\Tests\EventSubscriberTest;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class AuthSubscriberTest extends EventSubscriberTest
{
    public function testEventSubscription(): void
    {
        $this->assertArrayHasKey(LoginLinkRequestedEvent::class, AuthSubscriber::getSubscribedEvents());
    }

    public function testSendEmail(): void
    {
        $user = (new User())
            ->setEmail('test@samyqais.fr');
        $mailer = self::getContainer()->get(MailerService::class);
        $loginLinkHandler = $this->createMock(LoginLinkHandlerInterface::class);
        $subscriber = new AuthSubscriber($mailer, $loginLinkHandler);
        $event = new LoginLinkRequestedEvent($user);
        $this->dispatch($subscriber, $event);

        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Votre lien de connexion !', $email->getSubject());
    }
}