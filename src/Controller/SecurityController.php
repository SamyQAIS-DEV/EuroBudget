<?php

namespace App\Controller;

use App\Event\LoginLinkRequestedEvent;
use App\Form\LoginFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    public const LOGIN_ROUTE_NAME = 'login';

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
            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user === null) {
                $this->addFlash('danger', 'This email does not exist');

                return $this->redirectToRoute(self::LOGIN_ROUTE_NAME);
            }

            $dispatcher->dispatch(new LoginLinkRequestedEvent($user));
            $this->addFlash('success', 'Login link sent');

            return $this->redirectToRoute(self::LOGIN_ROUTE_NAME);
        }

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/login-link-check', name: 'login_link_check')]
    public function loginLinkCheck(): void
    {
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
    }
}
