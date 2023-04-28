<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DepositAccountAccess extends Constraint
{
    public string $message = 'The deposit account is not yours.';
}
