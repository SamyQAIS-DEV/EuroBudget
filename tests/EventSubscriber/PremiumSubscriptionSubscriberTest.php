<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\Event\PremiumSubscriptionEvent;
use App\EventSubscriber\PremiumSubscriptionSubscriber;
use App\Service\MailerService;
use App\Tests\EventSubscriberTest;

class PremiumSubscriptionSubscriberTest extends EventSubscriberTest
{
    private MailerService $mailerService;

    public function setUp(): void
    {
        parent::setUp();
        $this->mailerService = self::getContainer()->get(MailerService::class);
    }

    public function testEventSubscription(): void
    {
        $this->assertArrayHasKey(PremiumSubscriptionEvent::class, PremiumSubscriptionSubscriber::getSubscribedEvents());
    }

    public function testSendEmail(): void
    {
        /** @var User $user */
        ['user1' => $user] = $this->loadFixtures(['users']);
        $subscriber = new PremiumSubscriptionSubscriber($this->mailerService);
        $event = new PremiumSubscriptionEvent($user);
        $this->dispatch($subscriber, $event);
        $email = self::getMailerMessage();
        $this->assertSame('EuroBudget | Merci pour votre paiement !', $email->getSubject());
    }
}