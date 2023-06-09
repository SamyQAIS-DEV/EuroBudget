<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CategoryAccess extends Constraint
{
    public string $message = 'The category is not yours.';
}
