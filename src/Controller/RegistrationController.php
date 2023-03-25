<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\AlertEnum;
use App\Event\LoginLinkRequestedEvent;
use App\Form\RegistrationFormType;
use App\Security\Authentication\Authenticator;
use App\Service\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    public const REGISTER_ROUTE_NAME = 'auth_register';

    #[Route('/inscription', name: self::REGISTER_ROUTE_NAME)]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        SocialLoginService $socialLoginService,
        EventDispatcherInterface $dispatcher,
        UserAuthenticatorInterface $authenticator,
        Authenticator $appAuthenticator,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
        }

        $user = new User();
        // Si l'utilisateur provient de l'oauth, on préremplit ses données
        $isOauthUser = $request->get('oauth') && $socialLoginService->hydrate($request->getSession(), $user);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $user->setCreatedAt(new \DateTime());
            $user->setEmail(strtolower($user->getEmail()));
            $entityManager->persist($user);
            $entityManager->flush();
            $dispatcher->dispatch(new LoginLinkRequestedEvent($user, $isOauthUser));

            if ($isOauthUser) {
                // TODO : TRAD
                $this->addAlert(
                    AlertEnum::SUCCESS,
                    'Votre compte a bien été créé.'
                );

                return $authenticator->authenticateUser($user, $appAuthenticator, $request) ?: $this->redirectToRoute(HomeController::HOME_ROUTE_NAME);
            }

            // TODO : TRAD
            $this->addAlert(
                AlertEnum::SUCCESS,
                'Un message avec un lien de connexion vous a été envoyé par mail. Ce site n\'utilise pas de mot de passe.'
            );

            return $this->redirectToRoute(self::REGISTER_ROUTE_NAME);
        }

        return $this->render('auth/register.html.twig', [
            'registrationForm' => $form->createView(),
            'menu' => 'register',
            'oauth_registration' => $request->get('oauth'),
            'oauth_type' => $socialLoginService->getOauthType($request->getSession()),
        ]);
    }
}
