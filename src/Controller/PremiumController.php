<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{
    #[Route('/premium', name: 'premium')]
    public function index(): Response
    {
        return $this->render('pages/premium.html.twig', [

            'menu' => 'premium',
        ]);
    }
}
