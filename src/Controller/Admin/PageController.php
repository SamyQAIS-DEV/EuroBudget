<?php

namespace App\Controller\Admin;

use App\Enum\AlertEnum;
use App\Event\LoginLinkRequestedEvent;
use App\Helper\TimeHelper;
use App\Repository\TransactionRepository;
use App\Service\LoginLinkService;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('admin/pages/home.html.twig', [
            'months' => $transactionRepository->getMonthlyRevenues(),
            'menu' => 'home',
        ]);
    }

    #[Route(path: '/mailtest', name: 'mailtest', methods: ['POST'])]
    public function testMail(EventDispatcherInterface $dispatcher): RedirectResponse
    {
        $dispatcher->dispatch(new LoginLinkRequestedEvent($this->getUserOrThrow(), false));
        $this->addAlert(AlertEnum::SUCCESS, "L'email de test a bien été envoyé");

        return $this->redirectToRoute('admin_home');
    }
}