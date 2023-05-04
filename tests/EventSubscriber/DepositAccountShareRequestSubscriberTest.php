<?php

namespace App\Tests\EventSubscriber;

use App\Dto\DepositAccountShareRequestDto;
use App\Entity\User;
use App\Entity\UserRequest;
use App\Event\DepositAccountShareRequestAnsweredEvent;
use App\Event\DepositAccountShareRequestCreatedEvent;
use App\EventSubscriber\DepositAccountShareRequestSubscriber;
use App\Repository\DepositAccountRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRequestRepository;
use App\Service\NotificationService;
use App\Service\UserRequestService;
use App\Tests\EventSubscriberTest;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DepositAccountShareRequestSubscriberTest extends EventSubscriberTest
{
    private UserRequestService $userRequestService;
    private NotificationService $notificationService;
    private DepositAccountRepository $depositAccountRepository;
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;
    private DepositAccountShareRequestSubscriber $subscriber;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRequestService = self::getContainer()->get(UserRequestService::class);
        $this->notificationService = self::getContainer()->get(NotificationService::class);
        $this->depositAccountRepository = self::getContainer()->get(DepositAccountRepository::class);
        $this->security = self::getContainer()->get(Security::class);
        $this->urlGenerator = self::getContainer()->get(UrlGeneratorInterface::class);
        $this->subscriber = new DepositAccountShareRequestSubscriber($this->userRequestService, $this->notificationService, $this->depositAccountRepository, $this->security, $this->urlGenerator);;
    }

    public function testEventSubscription(): void
    {
        $this->assertArrayHasKey(DepositAccountShareRequestCreatedEvent::class, DepositAccountShareRequestSubscriber::getSubscribedEvents());
    }

    public function testOnDepositAccountShareRequestCreated(): void
    {
        /** @var User $user */
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users', 'notifications', 'user-requests']);
        $this->login($user1);
        $userRequestRepository = self::getContainer()->get(UserRequestRepository::class);
        $notificationRepository = self::getContainer()->get(NotificationRepository::class);
        $originalUserRequestCount = $userRequestRepository->count([]);
        $originalNotificationCount = $notificationRepository->count([]);
        $this->assertSame(5, $originalUserRequestCount);
        $this->assertSame(5, $originalNotificationCount);

        $depositAccountShareRequest = new DepositAccountShareRequestDto($user2);
        $depositAccountShareRequest->depositAccount = $user1->getFavoriteDepositAccount();
        $event = new DepositAccountShareRequestCreatedEvent($depositAccountShareRequest);
        $this->dispatch($this->subscriber, $event);

        $userRequestCount = $userRequestRepository->count([]);
        $notificationCount = $notificationRepository->count([]);
        $this->assertSame(6, $userRequestCount);
        $this->assertSame(6, $notificationCount);
    }

    public function testOnDepositAccountShareRequestAnsweredAccepted(): void
    {
        /** @var User $user */
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users', 'notifications']);
        $this->login($user1);
        $notificationRepository = self::getContainer()->get(NotificationRepository::class);
        $originalNotificationCount = $notificationRepository->count([]);
        $this->assertSame(5, $originalNotificationCount);
        $this->assertSame(false, $user1->getFavoriteDepositAccount()->getUsers()->contains($user2));

        $request = (new UserRequest())
            ->setCreator($user1)
            ->setTarget($user2)
            ->setAccepted(true)
            ->setEntity($this->callMethod($this->userRequestService, 'getHashForEntity', [$user1->getFavoriteDepositAccount()]));
        $event = new DepositAccountShareRequestAnsweredEvent($request);
        $this->dispatch($this->subscriber, $event);

        $notificationCount = $notificationRepository->count([]);
        $this->assertSame(6, $notificationCount);
        $this->assertSame(true, $user1->getFavoriteDepositAccount()->getUsers()->contains($user2));
    }

    public function testOnDepositAccountShareRequestAnsweredRejected(): void
    {
        /** @var User $user */
        ['user1' => $user1, 'user2' => $user2] = $this->loadFixtures(['users', 'notifications']);
        $this->login($user1);
        $notificationRepository = self::getContainer()->get(NotificationRepository::class);
        $originalNotificationCount = $notificationRepository->count([]);
        $this->assertSame(5, $originalNotificationCount);
        $this->assertSame(false, $user1->getFavoriteDepositAccount()->getUsers()->contains($user2));

        $request = (new UserRequest())
            ->setCreator($user1)
            ->setTarget($user2)
            ->setRejected(true)
            ->setEntity($this->callMethod($this->userRequestService, 'getHashForEntity', [$user1->getFavoriteDepositAccount()]));
        $event = new DepositAccountShareRequestAnsweredEvent($request);
        $this->dispatch($this->subscriber, $event);

        $notificationCount = $notificationRepository->count([]);
        $this->assertSame(6, $notificationCount);
        $this->assertSame(false, $user1->getFavoriteDepositAccount()->getUsers()->contains($user2));
    }
}