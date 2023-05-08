<?php

namespace App\Controller\Admin;

use App\Enum\AlertEnum;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin', name: 'admin_')]
class CacheController extends AbstractController
{
    #[Route(path: '/cache/clean', name: 'cache_clean', methods: ['POST'])]
    public function clean(CacheItemPoolInterface $cache): RedirectResponse
    {
        if ($cache->clear()) {
            $this->addAlert(AlertEnum::SUCCESS, 'Le cache a été supprimé');
        } else {
            $this->addFlash(AlertEnum::ERROR, "Le cache n'a pas pu être supprimé");
        }

        return $this->redirectToRoute('admin_home');
    }
}
