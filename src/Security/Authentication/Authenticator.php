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
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class Authenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly UrlMatcherInterface $urlMatcher
    ) {
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

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(SecurityController::LOGIN_ROUTE_NAME);
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $url = $this->getLoginUrl($request);

        if ('json' === $request->getContentTypeFormat()) {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }

        return new RedirectResponse($url);
    }
}
