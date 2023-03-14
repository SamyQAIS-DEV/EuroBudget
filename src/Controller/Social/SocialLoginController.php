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

class SocialLoginController extends AbstractController
{
    private const SCOPES = [
        'github' => ['user:email'],
        'discord' => ['identify', 'email'],
    ];

    public function __construct(private readonly ClientRegistry $clientRegistry)
    {
    }

    #[Route('/oauth/connect/{service}', name: 'oauth_connect')]
    public function connect(string $service): RedirectResponse
    {
        $this->ensureServiceAccepted($service);

        return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service], ['a' => 1]);
    }

    #[Route('/oauth/unlink/{service}', name: 'oauth_unlink')]
    #[IsGranted('ROLE_USER')]
    public function disconnect(string $service, AuthService $authService, EntityManagerInterface $entityManager): RedirectResponse
    {
        $this->ensureServiceAccepted($service);
        $method = 'set' . ucfirst($service) . 'Id';
        $authService->getUser()->$method(null);
        $entityManager->flush();
        // TODO : TRAD
        $this->addFlash('success', 'Votre compte a bien été dissocié de ' . $service);

        return $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
    }

    #[Route('/oauth/check/{service}', name: 'oauth_check')]
    public function check(): Response
    {
        return new Response();
    }

    private function ensureServiceAccepted(string $service): void
    {
        if (!array_key_exists($service, self::SCOPES)) {
            throw new AccessDeniedException();
        }
    }
}
