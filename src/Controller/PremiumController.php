<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PremiumController extends AbstractController
{
    #[Route(path: '/premium', name: 'premium')]
    public function index(PlanRepository $planRepository): Response
    {
        $plans = $planRepository->findAll();

        return $this->render('pages/premium.html.twig', [
            'plans' => $plans,
            'menu' => 'premium',
        ]);
    }
}
