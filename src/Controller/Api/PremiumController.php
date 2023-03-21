<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Event\PaymentEvent;
use App\Exception\PaymentFailedException;
use App\Repository\PlanRepository;
use App\Service\PaypalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class PremiumController extends AbstractController
{
    public function __construct(private readonly PlanRepository $planRepository)
    {
    }

    #[Route(path: '/api/premium/paypal/{orderId}', name: 'premium_paypal', methods: ['POST'])]
    public function paypal(string $orderId, PaypalService $paypal, EventDispatcherInterface $dispatcher): JsonResponse
    {
        try {
            $payment = $paypal->createPayment($orderId);
            $payment = $paypal->capture($payment);
            $plan = $this->planRepository->find($payment->planId);
            $dispatcher->dispatch(new PaymentEvent($payment, $plan, $this->getUser()));

            return $this->json([]);
        } catch (PaymentFailedException $e) {
            return $this->json(['title' => 'Erreur lors du paiement', 'detail' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}