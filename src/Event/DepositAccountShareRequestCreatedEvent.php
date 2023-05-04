<?php

namespace App\Event;

use App\Dto\DepositAccountShareRequestDto;

class DepositAccountShareRequestCreatedEvent
{
    public function __construct(
        private readonly DepositAccountShareRequestDto $depositAccountShareRequest,
    ) {
    }

    public function getDepositAccountShareRequest(): DepositAccountShareRequestDto
    {
        return $this->depositAccountShareRequest;
    }
}
