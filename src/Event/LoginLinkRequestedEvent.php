<?php

namespace App\Event;

use App\Entity\User;

class LoginLinkRequestedEvent
{
    public function __construct(
        private readonly User $user,
        private readonly bool $usingOauth = false,
        private readonly bool $registration = false
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

    public function isRegistration(): bool
    {
        return $this->registration;
    }
}
