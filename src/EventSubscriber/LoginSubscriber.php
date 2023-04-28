<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLogin',
        ];
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $event->getRequest()->getClientIp();
        if ($user instanceof User) {
            if (!$user->isEmailVerified()) {
                $user->setEmailVerified(true);
            }
            $ip = $event->getRequest()->getClientIp();
            if ($ip !== $user->getLastLoginIp()) {
                $user->setLastLoginIp($ip);
            }
            $user->setLastLoginAt(new DateTimeImmutable());
            $this->userRepository->save($user, true);
        }
    }
}
