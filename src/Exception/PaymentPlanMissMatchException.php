<?php

namespace App\Exception;

use Throwable;

class PaymentPlanMissMatchException extends \Exception
{
    public function __construct(string $message = "Le paiement ne correspond à aucun type d'abonnement", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
