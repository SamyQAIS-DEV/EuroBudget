<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PageController extends AbstractController
{
    #[Route(path: '/ui', name: 'ui')]
    #[IsGranted('ROLE_ADMIN')]
    public function ui(): Response
    {
        return $this->render('pages/ui.html.twig');
    }

    #[Route(path: '/components-testing', name: 'components-testing')]
    #[IsGranted('ROLE_ADMIN')]
    public function componentsTesting(): Response
    {
        return $this->render('pages/components-testing.html.twig');
    }
}
