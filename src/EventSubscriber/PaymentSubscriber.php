<?php

namespace App\EventSubscriber;

use App\Entity\Plan;
use App\Entity\Transaction;
use App\Event\PaymentEvent;
use App\Event\PremiumSubscriptionEvent;
use App\Exception\PaymentPlanMissMatchException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PaymentEvent::class => 'onPayment',
        ];
    }

    public function onPayment(PaymentEvent $event): void
    {
        // On regarde si le paiement correspond à un plan
        $payment = $event->getPayment();
        $plan = $event->getPlan();
        $user = $event->getUser();
        $planFromRepo = $this->entityManager->getRepository(Plan::class)->find($payment->planId);
        if ($plan->getPrice() !== $payment->amount || $planFromRepo === null) {
            throw new PaymentPlanMissMatchException();
        }
        $type = 'paypal';
        // On enregistre la transaction
        $transaction = (new Transaction())
            ->setPrice($payment->amount)
            ->setTax($payment->vat)
            ->setAuthor($event->getUser())
            ->setDuration($plan->getDuration())
            ->setMethod($type)
            ->setFirstname($payment->firstname)
            ->setLastname($payment->lastname)
            ->setCity($payment->city)
            ->setCountryCode($payment->countryCode)
            ->setAddress($payment->address)
            ->setPostalCode($payment->postalCode)
            ->setMethodRef($payment->id)
            ->setFee($payment->fee)
            ->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($transaction);
        // On met à jour la date de fin de premium de l'utilisateur
        $now = new \DateTimeImmutable();
        $premiumEnd = $user->getPremiumEnd() ?: new \DateTimeImmutable();
        // Si l'utilisateur a déjà une date de fin de premium dans le futur, alors on incrémentera son compte
        $premiumEnd = $premiumEnd > $now ? $premiumEnd : new \DateTimeImmutable();
        $user->setPremiumEnd($premiumEnd->add(new \DateInterval("P{$plan->getDuration()}M")));
        // Flush & dispatch
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new PremiumSubscriptionEvent($user));
    }
}
