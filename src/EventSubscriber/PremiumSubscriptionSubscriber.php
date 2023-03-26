<?php

namespace App\EventSubscriber;

use App\Event\PremiumSubscriptionEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PremiumSubscriptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerService $mailer
    ) {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PremiumSubscriptionEvent::class => 'onPremiumSubscription'
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onPremiumSubscription(PremiumSubscriptionEvent $event): void
    {
        $user = $event->getUser();

        $email = $this->mailer->createEmail('mails/payment/subscription.twig', 'Merci pour votre paiement !', [
            'username' => $user->getUserIdentifier(),
            'premiumEnd' => $user->getPremiumEnd()
        ])
            ->to($user->getEmail());
        $this->mailer->send($email);
    }
}