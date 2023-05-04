<?php

namespace App\Controller\Template;

use App\Controller\AbstractController;
use App\Repository\UserRequestRepository;
use Symfony\Component\HttpFoundation\Response;

class UserRequestStateController extends AbstractController
{
    public function state(UserRequestRepository $repository): Response
    {
        $user = $this->getUserOrThrow();
        $requestsCount = $repository->countUnansweredFor($user);

        return $this->render('partials/_bullet.html.twig', [
            'count' => $requestsCount,
        ]);
    }
}
