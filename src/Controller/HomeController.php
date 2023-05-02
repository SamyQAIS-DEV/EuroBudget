<?php

namespace App\Controller;

use App\Repository\OperationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const HOME_ROUTE_NAME = 'home';

    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly string $adminIps,
    ) {
    }

    #[Route(path: '/', name: self::HOME_ROUTE_NAME)]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->homeLogged();
        }

        return $this->render('pages/home.html.twig', [
            'canLoginAsAdmin' => str_contains($this->adminIps, $request->getClientIp()),
            'menu' => 'home',
        ]);
    }

    public function homeLogged(): Response
    {
        $favoriteDepositAccount = $this->getUserOrThrow()->getFavoriteDepositAccount();

        return $this->render('pages/home-logged.html.twig', [
            'labels' => $this->operationRepository->findLabelsFor($favoriteDepositAccount),
            'menu' => 'home',
        ]);
    }
}
