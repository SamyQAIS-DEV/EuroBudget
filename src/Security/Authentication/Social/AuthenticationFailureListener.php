<?php

namespace App\Security\Authentication\Social;

use App\Exception\UserAuthenticatedException;
use App\Exception\UserOauthNotFoundException;
use App\Service\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthenticationFailureListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $entityManager,
        private readonly SocialLoginService $socialLoginService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $exception = $event->getException();
        if ($exception instanceof UserOauthNotFoundException) {
            $this->onUserNotFound($exception);
        }
        if ($exception instanceof UserAuthenticatedException) {
            $this->onUserAlreadyAuthenticated($exception);
        }
    }

    public function onUserNotFound(UserOauthNotFoundException $exception): void
    {
        $this->socialLoginService->persist($this->requestStack->getSession(), $exception->getResourceOwner());
    }

    public function onUserAlreadyAuthenticated(UserAuthenticatedException $exception): void
    {
        $resourceOwner = $exception->getResourceOwner();
        $user = $exception->getUser();
        /** @var array{type: string} $data */
        $data = $this->normalizer->normalize($exception->getResourceOwner());
        $setter = 'set' . ucfirst($data['type']) . 'Id';
        $user->$setter($resourceOwner->getId());
        $this->entityManager->flush();
        $session = $this->requestStack->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add('success', 'Votre compte a bien été associé à ' . $data['type']);
        }
    }
}
