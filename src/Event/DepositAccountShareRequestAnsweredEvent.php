<?php

namespace App\Event;

use App\Entity\UserRequest;

class DepositAccountShareRequestAnsweredEvent
{
    public function __construct(
        private readonly UserRequest $request,
    ) {
    }

    public function getUserRequest(): UserRequest
    {
        return $this->request;
    }
}
