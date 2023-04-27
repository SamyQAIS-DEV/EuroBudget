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

#[Route(path: '/admin/mailpreview', name: 'admin_mailpreview_')]
class MailPreviewController extends AbstractController
{

    #[Route(path: '', name: 'index')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        return $this->render('admin/pages/mail-preview.html.twig', [
            'menu' => 'mail',
        ]);
    }

    #[Route(path: '/registration', name: 'registration', methods: ['GET'])]
    public function registration(LoginLinkService $loginLinkService, MailerService $mailer): Response
    {
        $user = $this->getUserOrThrow();
        $loginLink = $loginLinkService->createLoginLink($user);
        $email = $mailer->createEmail('mails/auth/registration.twig', 'Votre inscription !', [
            'token' => $loginLink->getToken(),
            'leftTime' => TimeHelper::leftTime($loginLink->getExpiresAt()),
            'username' => $user->getUserIdentifier()
        ]);
        return new Response($email->getHtmlBody());
    }

    #[Route(path: '/login-link', name: 'loginlink', methods: ['GET'])]
    public function loginLink(LoginLinkService $loginLinkService, MailerService $mailer): Response
    {
        $user = $this->getUserOrThrow();
        $loginLink = $loginLinkService->createLoginLink($user);
        $email = $mailer->createEmail('mails/auth/login_link.twig', 'Votre lien de connexion !', [
            'token' => $loginLink->getToken(),
            'leftTime' => TimeHelper::leftTime($loginLink->getExpiresAt()),
            'username' => $user->getUserIdentifier()
        ]);
        return new Response($email->getHtmlBody());
    }

    #[Route(path: '/delete-account', name: 'delete_account', methods: ['GET'])]
    public function deleteAccount(MailerService $mailer): Response
    {
        $user = $this->getUserOrThrow();
        $email = $mailer->createEmail('mails/auth/delete.twig', 'Suppression de votre compte !', [
            'days' => 5,
        ]);
        return new Response($email->getHtmlBody());
    }
}