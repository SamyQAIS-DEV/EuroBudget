<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\LoginLinkRequestedEvent;
use App\Form\RegistrationFormType;
use App\Service\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    public const REGISTER_ROUTE_NAME = 'register';

    #[Route('/inscription', name: self::REGISTER_ROUTE_NAME)]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        SocialLoginService $socialLoginService,
        EventDispatcherInterface $dispatcher
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
        }

        $user = new User();
        // Si l'utilisateur provient de l'oauth, on préremplit ses données
        $isOauthUser = $request->get('oauth') ? $socialLoginService->hydrate($user) : false;
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($user);
            $entityManager->flush();

            $dispatcher->dispatch(new LoginLinkRequestedEvent($user));
            $this->addFlash('success', 'Registered and login link sent');

            return $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
