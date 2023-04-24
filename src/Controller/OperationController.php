<?php

namespace App\Controller;

use App\Enum\AlertEnum;
use App\Security\Voter\OperationVoter;
use App\Service\OperationCreateFormInvoicesService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/operations', name: 'operation_')]
class OperationController extends AbstractController
{
    #[Route(path: '/create-from-invoices', name: 'create_from_invoices', methods: ['POST', 'GET'])]
    public function createFromInvoices(OperationCreateFormInvoicesService $operationCreateFormInvoicesService): Response
    {
        if (!$this->isGranted(OperationVoter::CAN_CREATE_FROM_INVOICES)) {
            $this->addAlert(AlertEnum::ERROR, 'Vous avez déjà convertis les factures en opérations ce mois-ci');
//
            return $this->redirectToRoute('home');
        }

        $count = $operationCreateFormInvoicesService->process($this->getUser());

        $message = ngettext('%d opération a bien été convertie depuis les factures', '%d opérations ont bien été converties depuis les factures', $count);
        $this->addAlert(AlertEnum::SUCCESS, $message);

        return $this->redirectToRoute('home');
    }
}
