<?php

namespace App\Validator;

use App\Entity\CategorizableInterface;
use App\Entity\Category;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CategoryAccessValidator extends ConstraintValidator
{
    /**
     * @param Category|null $value
     * @param CategoryAccess $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $object = $this->context->getObject();
        if (!$object instanceof CategorizableInterface) {
            throw new RuntimeException(sprintf('The entity %s must implement the %s', $object::class,  CategorizableInterface::class));
        }

        /** @var Category $category */
        $category = $value;
        if ($object->getDepositAccount()->getCategories()->contains($category)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
