<?php

namespace App\Controller\Admin;

use App\Exception\EntityNotFoundException;
use App\Repository\UserRepository;
use App\Security\Authentication\Authenticator;
use App\Security\Voter\AdminVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route(path: '/admin', name: 'admin_')]
class SecurityController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route(path: '/auto-login', name: 'auto_login')]
    #[IsGranted(AdminVoter::CAN_LOGIN)]
    public function index(
        Request $request,
        UserAuthenticatorInterface $authenticator,
        Authenticator $appAuthenticator,
    ): Response
    {
        $user = $this->userRepository->findOneByRole('ROLE_SUPER_ADMIN');
        if (!$user) {
            throw new EntityNotFoundException();
        }
        $authenticator->authenticateUser($user, $appAuthenticator, $request);

        return $this->redirectToRoute('admin_home');
    }
}