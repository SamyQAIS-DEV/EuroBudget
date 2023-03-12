<?php

namespace App\Infrastructure\Social\Authenticator;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

abstract class AbstractSocialAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        protected EntityManagerInterface $em,
        private readonly RouterInterface $router,
        //        private readonly AuthService $authService
    )
    {
    }

    /**
     * @throws Exception
     */
    public function supports(Request $request): bool
    {
        if ('' === $this->serviceName) {
            throw new RuntimeException('You must set a $serviceName property (for instance "github", "facebook")');
        }
        return $request->attributes->get('_route') === 'oauth_check' && $request->get('service') === $this->serviceName;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        dd('start');
        return new RedirectResponse($this->router->generate('auth_login'));
    }

    public function getCredentials(Request $request): AccessTokenInterface
    {
        dd('getCredentials');
        return $this->fetchAccessToken($this->getClient());
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->getClient();
        try {
            $accessToken = $client->getAccessToken();
        } catch (\Exception) {
            throw new CustomUserMessageAuthenticationException(sprintf("Une erreur est survenue lors de la récupération du token d'accès %s", $this->serviceName));
        }

        $resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);
        dd($resourceOwner);
        try {
            $resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);
        } catch (\Exception) {
            throw new CustomUserMessageAuthenticationException(sprintf('Une erreur est survenue lors de la communication avec %s', $this->serviceName));
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): RedirectResponse
    {
        dd('onAuthenticationSuccess');
        // TODO: Implement onAuthenticationSuccess() method.
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        dd('onAuthenticationFailure');
        // TODO: Implement onAuthenticationFailure() method.
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }
}