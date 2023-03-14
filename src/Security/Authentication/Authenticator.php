<?php

namespace App\Security\Authentication;

use App\Controller\HomeController;
use App\Controller\SecurityController;
use App\Repository\UserRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class Authenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly UrlMatcherInterface $urlMatcher
    ) {
    }

    public function supports(Request $request): bool
    {
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        $email = (string) $request->request->get('email', '');

        return new SelfValidatingPassport(
            new UserBadge($email, fn (string $email) => $this->userRepository->findForAuth($email)),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): RedirectResponse {
        if ($redirect = $request->get('redirect')) {
            try {
                $this->urlMatcher->match($redirect);

                return new RedirectResponse($redirect);
            } catch (\Exception) {
                // Do nothing
            }
        }
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate(HomeController::HOME_ROUTE_NAME));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
    }
}
