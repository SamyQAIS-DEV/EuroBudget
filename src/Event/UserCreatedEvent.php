<?php

namespace App\Event;

use App\Entity\User;

class UserCreatedEvent
{
    public function __construct(
        private readonly User $user,
        private readonly bool $usingOauth = false
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isUsingOauth(): bool
    {
        return $this->usingOauth;
    }
}
