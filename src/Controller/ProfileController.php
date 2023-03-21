<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'user_profil', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }
}
