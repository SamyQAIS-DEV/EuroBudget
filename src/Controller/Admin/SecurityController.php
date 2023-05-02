<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Security\Authentication\Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route(path: '/admin', name: 'admin_')]
class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly string $adminIps,
    ) {
    }

    #[Route(path: '/auto-login', name: 'auto_login')]
    public function index(
        Request $request,
        UserAuthenticatorInterface $authenticator,
        Authenticator $appAuthenticator,
    ): Response
    {
        if (!str_contains($this->adminIps, $request->getClientIp())) {
            throw new AccessDeniedException();
        }
        $user = $this->userRepository->find(1);
        $authenticator->authenticateUser($user, $appAuthenticator, $request);

        return $this->redirectToRoute('admin_home');
    }
}