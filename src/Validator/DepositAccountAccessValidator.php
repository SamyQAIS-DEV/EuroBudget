<?php

namespace App\Validator;

use App\Entity\DepositAccount;
use App\Entity\User;
use App\Exception\UnauthenticatedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DepositAccountAccessValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @param DepositAccount|null $value
     * @param DepositAccountAccess $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new UnauthenticatedException();
        }

        /** @var DepositAccount $depositAccount */
        $depositAccount = $value;
        if ($depositAccount->getUsers()->contains($user)) {
            return;
        }
        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
