<?php

namespace App\EventSubscriber;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Event\DepositAccountShareRequestCreatedEvent;
use App\Event\DepositAccountShareRequestAnsweredEvent;
use App\Exception\UnauthenticatedException;
use App\Repository\DepositAccountRepository;
use App\Service\NotificationService;
use App\Service\UserRequestService;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class DepositAccountShareRequestSubscriber implements EventSubscriberInterface
{
    public function __construct(
       private readonly UserRequestService $userRequestService,
       private readonly NotificationService $notificationService,
       private readonly DepositAccountRepository $depositAccountRepository,
       private readonly Security $security,
       private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DepositAccountShareRequestCreatedEvent::class => 'onDepositAccountShareRequestCreated',
            DepositAccountShareRequestAnsweredEvent::class => 'onDepositAccountShareRequestAnswered',
        ];
    }

    public function onDepositAccountShareRequestCreated(DepositAccountShareRequestCreatedEvent $event): void
    {
        $depositAccountShareRequest = $event->getDepositAccountShareRequest();
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new UnauthenticatedException();
        }

        $this->userRequestService->create($user, $depositAccountShareRequest->user, $depositAccountShareRequest->depositAccount);

        $this->notificationService->notifyUser($depositAccountShareRequest->user, sprintf(
            '%s a partagé un compte "%s" en banque avec vous',
            $user->getFullName(),
            $depositAccountShareRequest->depositAccount->getTitle()
        ), $this->urlGenerator->generate('user_requests'));
    }

    public function onDepositAccountShareRequestAnswered(DepositAccountShareRequestAnsweredEvent $event): void
    {
        $request = $event->getUserRequest();

        if ($request->isAnswered()) {
            throw new RuntimeException('Already answered');
        }

        $depositAccount = $this->depositAccountRepository->find($this->userRequestService->getIdFromHash($request->getEntity()));

        if (!$depositAccount instanceof DepositAccount) {
            throw new NotFoundResourceException();
        }

        if ($request->isAccepted()) {
            $depositAccount->addUser($request->getTarget());
        }

        $this->notificationService->notifyUser($request->getCreator(), sprintf(
            '%s a %s la demande de partage du compte "%s" que vous lui avez faite',
            $request->getTarget()->getFullName(),
            $request->isAccepted() ? 'accepté' : 'refusé',
            $depositAccount->getTitle()
        ));

        $this->depositAccountRepository->flush();
    }
}
