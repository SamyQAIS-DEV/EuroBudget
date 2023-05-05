<?php

namespace App\Controller;

use App\Repository\OperationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public const HOME_ROUTE_NAME = 'home';

    public function __construct(private readonly OperationRepository $operationRepository)
    {
    }

    #[Route(path: '/', name: self::HOME_ROUTE_NAME)]
    public function index(): Response
    {
        $this->dumpIp();
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
        $favoriteDepositAccount = $this->getUserOrThrow()->getFavoriteDepositAccount();

        return $this->render('pages/home-logged.html.twig', [
            'labels' => $this->operationRepository->findLabelsFor($favoriteDepositAccount),
            'menu' => 'home',
        ]);
    }

    private function dumpIp(): void
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //Check if visitor is from shared network
            $vIP = $_SERVER['HTTP_CLIENT_IP'];
            dump('HTTP_CLIENT_IP' . $_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //Check if visitor is using a proxy
            $vIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            dump('HTTP_X_FORWARDED_FOR' . $_SERVER['HTTP_X_FORWARDED_FOR']);
        } else { //check for the remote address of visitor.
            $vIP = $_SERVER['REMOTE_ADDR'];
            dump('REMOTE_ADDR' . $_SERVER['REMOTE_ADDR']);
        }
        dump('The visitors Real address : ' . $vIP);
    }
}
