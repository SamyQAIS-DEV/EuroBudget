<?php

namespace App\Controller;

use App\Enum\AlertEnum;
use App\Event\LoginLinkRequestedEvent;
use App\Form\LoginFormType;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    public const LOGIN_ROUTE_NAME = 'auth_login';
    public const CHECK_ROUTE_NAME = 'auth_check';
    public const LOGOUT_ROUTE_NAME = 'auth_logout';

    #[Route('/connexion', name: self::LOGIN_ROUTE_NAME)]
    public function login(
        Request $request,
        UserRepository $userRepository,
        EventDispatcherInterface $dispatcher
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
        }

        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            if ($user === null) {
                // TODO : TRAD
                $this->addAlert(AlertEnum::ERROR, 'This email does not exist');

                return $this->redirectToRoute(self::LOGIN_ROUTE_NAME);
            }

            $dispatcher->dispatch(new LoginLinkRequestedEvent($user));
            // TODO : TRAD
            $this->addAlert(AlertEnum::SUCCESS, 'Login link sent');

            return $this->redirectToRoute(self::LOGIN_ROUTE_NAME);
        }

        return $this->render('auth/login.html.twig', [
            'loginForm' => $form->createView(),
        ]);
    }

    #[Route('/auth/check/{token}', name: self::CHECK_ROUTE_NAME)]
    public function check(): Response
    {
        throw $this->createNotFoundException();
    }

    #[Route('/logout', name: self::LOGOUT_ROUTE_NAME)]
    public function logout(): void
    {
    }
}
