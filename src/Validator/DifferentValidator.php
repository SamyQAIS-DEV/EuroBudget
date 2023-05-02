<?php

namespace App\Validator;

use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DifferentValidator extends ConstraintValidator
{
    /**
     * @param object|null $value
     * @param Different $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof Different) {
            throw new \RuntimeException(sprintf('%s ne peut pas valider des contraintes %s', self::class, $constraint::class));
        }

        $accessor = new PropertyAccessor();
        $itemA = $accessor->getValue($value, $constraint->fieldA);
        $itemB = $accessor->getValue($value, $constraint->fieldB);

        if (!method_exists($itemA, 'getId') || !method_exists($itemB, 'getId') || $itemA->getId() === $itemB->getId()) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->fieldA)
                ->addViolation();
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->fieldB)
                ->addViolation();
        }
    }
}