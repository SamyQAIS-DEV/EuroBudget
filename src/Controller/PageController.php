<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/ui', name: 'ui')]
    public function ui(): Response
    {
        return $this->render('pages/ui.html.twig');
    }

    #[Route('/components-testing', name: 'components-testing')]
    public function componentsTesting(): Response
    {
        return $this->render('pages/components-testing.html.twig');
    }
}
