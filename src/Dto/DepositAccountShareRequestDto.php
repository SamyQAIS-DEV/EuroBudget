<?php

namespace App\Dto;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Validator\DepositAccountAccess;
use App\Validator\Different;
use Symfony\Component\Validator\Constraints as Assert;

class DepositAccountShareRequestDto
{
    #[Assert\NotBlank]
    #[DepositAccountAccess]
    public DepositAccount $depositAccount;

    public function __construct(
        #[Assert\NotBlank]
        public User $user
    ) {
    }
}