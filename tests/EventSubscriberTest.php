<?php

namespace App\Tests;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class EventSubscriberTest extends WebTestCase
{
    protected function dispatch(EventSubscriberInterface $subscriber, object $event): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch($event);
    }

    /**
     * Vérifie qu'un subscriber écoute bien un évènement donnée au niveau du kernel.
     */
    protected function assertSubscribeTo(string $subscriberClass, string $event): void
    {
        self::bootKernel();
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $subscribers = $dispatcher->getListeners($event);
        $subscribers = array_map(fn ($subscriber) => $subscriber[0]::class, $subscribers);
        $this->assertContains($subscriberClass, $subscribers);
    }
}
