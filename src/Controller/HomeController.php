<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const HOME_ROUTE_NAME = 'home';

    #[Route('/', name: self::HOME_ROUTE_NAME)]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->homeLogged();
        }

        return $this->render('pages/home.html.twig', [
            'menu' => 'home',
        ]);
    }

    public function homeLogged(): Response
    {
        return $this->render('pages/home-logged.html.twig', [
            'menu' => 'home',
        ]);
    }
}
