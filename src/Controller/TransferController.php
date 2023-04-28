<?php

namespace App\Controller;

use App\Dto\TransferDto;
use App\Enum\AlertEnum;
use App\Form\TransferFormType;
use App\Service\TransferService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/transfers', name: 'transfers_')]
class TransferController extends AbstractController
{
    public function __construct(
        private readonly TransferService $transferService,
    )
    {
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $transfer = new TransferDto();
        $form = $this->createForm(TransferFormType::class, $transfer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->transferService->create($transfer, $this->getUser());
            } catch (Throwable $exception) {
                $this->addAlert(AlertEnum::ERROR, 'Une erreur est survenue');

                return $this->render('transfer/new.html.twig', [
                    'form' => $form->createView(),
                    'menu' => 'transfers',
                ]);
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('transfer/new.html.twig', [
            'form' => $form->createView(),
            'menu' => 'transfers',
        ]);
    }
}
