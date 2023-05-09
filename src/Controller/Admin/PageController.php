<?php

namespace App\Controller\Admin;

use App\Enum\AlertEnum;
use App\Event\LoginLinkRequestedEvent;
use App\Helper\TimeHelper;
use App\Repository\CategoryRepository;
use App\Repository\DepositAccountRepository;
use App\Repository\InvoiceRepository;
use App\Repository\NotificationRepository;
use App\Repository\OperationRepository;
use App\Repository\TransactionRepository;
use App\Repository\UserRequestRepository;
use App\Service\LoginLinkService;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin', name: 'admin_')]
class PageController extends AbstractController
{
    #[Route(path: '', name: 'home')]
    public function index(
        CategoryRepository $categoryRepository,
        DepositAccountRepository $depositAccountRepository,
        InvoiceRepository $invoiceRepository,
        NotificationRepository $notificationRepository,
        OperationRepository $operationRepository,
        TransactionRepository $transactionRepository,
        UserRequestRepository $userRequestRepository,
    ): Response {
        return $this->render('admin/pages/home.html.twig', [
            'categoryCount' => $categoryRepository->count([]),
            'depositAccountCount' => $depositAccountRepository->count([]),
            'invoiceCount' => $invoiceRepository->count([]),
            'notificationCount' => $notificationRepository->count([]),
            'operationCount' => $operationRepository->count([]),
            'transactionCount' => $transactionRepository->count([]),
            'userRequestCount' => $userRequestRepository->count([]),
            'months' => $transactionRepository->getMonthlyRevenues(),
            'menu' => 'home',
        ]);
    }

    #[Route(path: '/mailtest', name: 'mailtest', methods: ['POST'])]
    public function testMail(EventDispatcherInterface $dispatcher): RedirectResponse
    {
        $dispatcher->dispatch(new LoginLinkRequestedEvent($this->getUserOrThrow()));
        $this->addAlert(AlertEnum::SUCCESS, "L'email de test a bien été envoyé");

        return $this->redirectToRoute('admin_home');
    }
}