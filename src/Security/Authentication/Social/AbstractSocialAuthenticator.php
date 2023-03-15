<?php

namespace App\Security\Authentication\Social;

use App\Controller\HomeController;
use App\Controller\RegistrationController;
use App\Controller\SecurityController;
use App\Controller\Social\SocialLoginController;
use App\Entity\User;
use App\Infrastructure\Social\Exception\UserAuthenticatedException;
use App\Infrastructure\Social\Exception\UserOauthNotFoundException;
use App\Repository\UserRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

abstract class AbstractSocialAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        protected readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        private readonly AuthService $authService
    ) {
    }

    /**
     * @throws Exception
     */
    public function supports(Request $request): bool
    {
        if ('' === $this->serviceName) {
            throw new RuntimeException('You must set a $serviceName property (for instance "github", "discord")');
        }

        return $request->attributes->get('_route') === SocialLoginController::CHECK_ROUTE_NAME &&
            $request->get('service') === $this->serviceName;
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->getClient();
        try {
            $accessToken = $client->getAccessToken();
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException(sprintf("Une erreur est survenue lors de la récupération du token d'accès %s", $this->serviceName));
        }

        try {
            $resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException(sprintf('Une erreur est survenue lors de la communication avec %s', $this->serviceName));
        }
        /** @var UserRepository $repository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $this->authService->getUserOrNull();
        if ($user) {
            throw new UserAuthenticatedException($user, $resourceOwner);
        }
        $user = $this->getUserFromResourceOwner($resourceOwner, $userRepository);
        if (null === $user) {
            throw new UserOauthNotFoundException($resourceOwner);
        }

        $userLoader = fn () => $user;

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), $userLoader),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        $request->request->set('_remember_me', '1');

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate(HomeController::HOME_ROUTE_NAME));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        if ($exception instanceof UserOauthNotFoundException) {
            return new RedirectResponse($this->router->generate(RegistrationController::REGISTER_ROUTE_NAME, ['oauth' => 1]));
        }

        // TODO : Tests association compte déjà connecté
        if ($exception instanceof UserAuthenticatedException) {
            return new RedirectResponse($this->router->generate('user_edit'));
        }

        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate(SecurityController::LOGIN_ROUTE_NAME));
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    protected function getUserFromResourceOwner(
        ResourceOwnerInterface $resourceOwner,
        UserRepository $repository
    ): ?User {
        return null;
    }

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }
}