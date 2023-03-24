<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Payment;
use App\Entity\Plan;
use App\Entity\Transaction;
use App\Entity\User;
use App\Event\PaymentEvent;
use App\Event\PremiumSubscriptionEvent;
use App\EventSubscriber\PaymentSubscriber;
use App\Exception\PaymentPlanMissMatchException;
use App\Repository\PlanRepository;
use App\Tests\EventSubscriberTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;

class PaymentSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(PaymentSubscriber::class, PaymentEvent::class);
    }

    private function getSubscriber(): array
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $planRepository = $this->createMock(PlanRepository::class);
        $planRepository
            ->expects($this->once())
            ->method('find')
            ->willReturnCallback(fn ($params) => $params === 1 ? $this->getPlan() : null);
        $entityManager->expects($this->once())->method('getRepository')->with(Plan::class)->willReturn($planRepository);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->any())->method('dispatch');

        $subscriber = new PaymentSubscriber($entityManager, $dispatcher);

        return [$subscriber, $entityManager, $dispatcher];
    }

    private function getEvent(): PaymentEvent
    {
        $payment = new Payment();
        $payment->id = 'platform_id';
        $payment->amount = 100;
        $payment->planId = 1;
        $user = new User();

        return new PaymentEvent($payment, $this->getPlan(), $user);
    }

    private function getPlan(): Plan
    {
        return (new Plan())
            ->setPrice(100)
            ->setDuration(12);
    }

    public function testThrowExceptionIfNoPlanIsFound()
    {
        [$subscriber] = $this->getSubscriber();
        $event = $this->getEvent();
        $event->getPayment()->planId = 999;
        $this->expectException(PaymentPlanMissMatchException::class);
        $this->dispatch($subscriber, $event);
    }

    public function testPersistTransaction()
    {
        /** @var MockObject $entityManager */
        [$subscriber, $entityManager] = $this->getSubscriber();
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(fn (Transaction $transaction) => 100 == $transaction->getPrice()));
        $entityManager->expects($this->once())->method('flush');
        $this->dispatch($subscriber, $this->getEvent());
    }

    public function testDispatchPremiumEvent()
    {
        /** @var MockObject $dispatcher */
        [$subscriber, $entityManager, $dispatcher] = $this->getSubscriber();
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PremiumSubscriptionEvent::class));
        $this->dispatch($subscriber, $this->getEvent());
    }

    public function testPushPremiumEndTime()
    {
        [$subscriber] = $this->getSubscriber();
        $event = $this->getEvent();
        $user = $event->getUser();
        $this->dispatch($subscriber, $event);
        $this->assertGreaterThan(
            (new \DateTimeImmutable('+ 10 months'))->getTimestamp(),
            $user->getPremiumEnd()->getTimestamp()
        );
    }

    public function testPushPremiumEndTimeFurther()
    {
        [$subscriber] = $this->getSubscriber();
        $event = $this->getEvent();
        $user = $event->getUser();
        $user->setPremiumEnd(new \DateTimeImmutable('+ 10 months'));
        $this->dispatch($subscriber, $event);
        $this->assertGreaterThan(
            (new \DateTimeImmutable('+ 20 months'))->getTimestamp(),
            $user->getPremiumEnd()->getTimestamp()
        );
    }
}
