<?php

namespace App\Service;

use App\Entity\Payment;

class PaypalService
{
    public function createPayment(string $orderId): Payment
    {
        try {

        } catch (HttpException $e) {
            throw PaymentFailedException::fromPaypalHttpException($e);
        }
    }
}