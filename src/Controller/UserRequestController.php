<?php

namespace App\Controller;

use App\Entity\UserRequest;
use App\Event\DepositAccountShareRequestAnsweredEvent;
use App\Repository\NotificationRepository;
use App\Repository\UserRequestRepository;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserRequestController extends AbstractController
{
    public function __construct(
        private readonly UserRequestRepository $userRequestRepository,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    #[Route(path: '/profil/demandes', name: 'user_requests', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $requests = $this->userRequestRepository->findFor($user);

        return $this->render('profile/requests.html.twig', [
            'requests' => $requests,
            'menu' => 'profile',
        ]);
    }

    #[Route(path: '/profil/demandes/{id}/accept', name: 'user_requests_accept', methods: ['POST'])]
    // TODO Voter
    public function accept(UserRequest $request): Response
    {
        $request->setAccepted(true);
        $this->dispatcher->dispatch(new DepositAccountShareRequestAnsweredEvent($request));
        $this->userRequestRepository->flush();

        return $this->redirectToRoute('user_requests');
    }

    #[Route(path: '/profil/demandes/{id}/reject', name: 'user_requests_reject', methods: ['POST'])]
    // TODO Voter
    public function reject(UserRequest $request): Response
    {
        $request->setRejected(true);
        $this->dispatcher->dispatch(new DepositAccountShareRequestAnsweredEvent($request));
        $this->userRequestRepository->flush();

        return $this->redirectToRoute('user_requests');
    }
}
