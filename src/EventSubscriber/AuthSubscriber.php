<?php

namespace App\EventSubscriber;

use App\Entity\LoginLink;
use App\Event\LoginLinkRequestedEvent;
use App\Helper\TimeHelper;
use App\Service\LoginLinkService;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly LoginLinkService $loginLinkService,
    ) {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginLinkRequestedEvent::class => 'onLoginLinkRequested'
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onLoginLinkRequested(LoginLinkRequestedEvent $event): void
    {
        if ($event->isUsingOauth()) {
            return;
        }
        $user = $event->getUser();
        $loginLink = $this->loginLinkService->createLoginLink($user);

        $email = $this->mailer->createEmail('mails/auth/login_link.twig', 'Votre lien de connexion !', [
            'token' => $loginLink->getToken(),
            'leftTime' => TimeHelper::leftTime($loginLink->getExpiresAt()),
            'username' => $user->getUserIdentifier()
        ])
            ->to($user->getEmail());
        $this->mailer->send($email);
    }
}
