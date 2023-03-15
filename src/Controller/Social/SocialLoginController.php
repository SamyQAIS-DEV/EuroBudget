<?php

namespace App\Controller\Social;

use App\Controller\HomeController;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/oauth')]
class SocialLoginController extends AbstractController
{
    public const CONNECT_ROUTE_NAME = 'oauth_connect';
    public const UNLINK_ROUTE_NAME = 'oauth_unlink';
    public const CHECK_ROUTE_NAME = 'oauth_check';

    private const SCOPES = [
        'github' => ['user:email'],
        'discord' => ['identify', 'email'],
    ];

    public function __construct(private readonly ClientRegistry $clientRegistry)
    {
    }

    #[Route('/connect/{service}', name: self::CONNECT_ROUTE_NAME)]
    public function connect(string $service): RedirectResponse
    {
        $this->ensureServiceAccepted($service);

        return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service], ['a' => 1]);
    }

    #[Route('/check/{service}', name: self::CHECK_ROUTE_NAME)]
    public function check(): Response
    {
        throw $this->createNotFoundException();
    }

    #[Route('/unlink/{service}', name: self::UNLINK_ROUTE_NAME)]
    #[IsGranted('ROLE_USER')]
    public function unlink(string $service, AuthService $authService, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->ensureServiceAccepted($service);
        $method = 'set' . ucfirst($service) . 'Id';
        $authService->getUser()->$method(null);
        $entityManager->flush();
        // TODO : TRAD
        $this->addFlash('success', 'Votre compte a bien été dissocié de ' . $service);

        return $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
    }

    private function ensureServiceAccepted(string $service): void
    {
        if (!array_key_exists($service, self::SCOPES)) {
            throw new AccessDeniedException();
        }
    }
}
