<?php

namespace App\EventSubscriber;

use App\Entity\DepositAccount;
use App\Event\LoginLinkRequestedEvent;
use App\Event\UserCreatedEvent;
use App\Helper\TimeHelper;
use App\Service\LoginLinkService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly LoginLinkService $loginLinkService,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginLinkRequestedEvent::class => 'onLoginLinkRequested',
            UserCreatedEvent::class => 'onUserCreated',
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onLoginLinkRequested(LoginLinkRequestedEvent $event): void
    {
        $user = $event->getUser();
        $loginLink = $this->loginLinkService->createLoginLink($user);
        $email = $this->mailer->createEmail('mails/auth/login_link.twig', 'Votre lien de connexion !', [
            'token' => $loginLink->getToken(),
            'leftTime' => TimeHelper::leftTime($loginLink->getExpiresAt()),
            'username' => $user->getUserIdentifier(),
        ])
            ->to($user->getEmail());
        $this->mailer->send($email);
    }

    public function onUserCreated(UserCreatedEvent $event): void
    {
        $user = $event->getUser();
        $depositAccount = new DepositAccount();
        $depositAccount->setTitle('Compte de ' . $user->getUserIdentifier())
            ->setCreator($user);
        $user->setFavoriteDepositAccount($depositAccount);
        $this->entityManager->persist($depositAccount);
        $email = $this->mailer->createEmail('mails/auth/registration.twig', 'Votre inscription !', [
            'isUsingOauth' => $event->isUsingOauth(),
            'username' => $user->getUserIdentifier(),
        ])
            ->to($user->getEmail());
        $this->mailer->send($email);
        if ($event->isUsingOauth()) {
            return;
        }
        $this->dispatcher->dispatch(new LoginLinkRequestedEvent($user));
    }
}
