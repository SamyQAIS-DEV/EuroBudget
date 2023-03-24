<?php

namespace App\Event;

use App\Entity\Payment;
use App\Entity\Plan;
use App\Entity\User;

class PaymentEvent
{
    public function __construct(
        private readonly Payment $payment,
        private readonly Plan $plan,
        private readonly User $user
    ) {
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
