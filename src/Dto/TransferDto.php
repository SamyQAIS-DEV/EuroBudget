<?php

namespace App\Dto;

use App\Entity\DepositAccount;
use App\Validator\DepositAccountAccess;
use App\Validator\Different;
use Symfony\Component\Validator\Constraints as Assert;

#[Different(fieldA: 'fromDepositAccount', fieldB: 'targetDepositAccount', entityClass: DepositAccount::class)]
class TransferDto
{
    #[Assert\NotBlank]
    #[DepositAccountAccess]
    public DepositAccount $fromDepositAccount;

    #[Assert\NotBlank]
    #[DepositAccountAccess]
    public DepositAccount $targetDepositAccount;

    #[Assert\Positive]
    #[Assert\NotNull]
    public float $amount;
}