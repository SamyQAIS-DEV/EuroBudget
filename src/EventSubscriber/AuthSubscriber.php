<?php

namespace App\EventSubscriber;

use App\Event\LoginLinkRequestedEvent;
use App\Helper\TimeHelper;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerService $mailer,
        private readonly LoginLinkHandlerInterface $loginLinkHandler
    ) {
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginLinkRequestedEvent::class => 'onLoginLinkRequest'
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function onLoginLinkRequest(LoginLinkRequestedEvent $event): void
    {
        $user = $event->getUser();
        $loginLink = $this->loginLinkHandler->createLoginLink($user);

        $email = $this->mailer->createEmail('mails/auth/login_link.twig', 'Votre lien de connexion !', [
            'username' => $user->getUserIdentifier(),
            'leftTime' => TimeHelper::leftTime($loginLink->getExpiresAt()),
            'loginUrl' => $loginLink->getUrl()
        ])
            ->to($user->getEmail());
        $this->mailer->send($email);
    }
}
