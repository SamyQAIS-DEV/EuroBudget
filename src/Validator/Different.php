<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Different extends Constraint
{
    public string $message = 'The values are the same.';

    public string $fieldA = '';

    public string $fieldB = '';

    public ?string $entityClass = null;

    #[HasNamedArguments]
    public function __construct(
        string $fieldA = '',
        string $fieldB = '',
        string $message = 'The values are the same.',
        string $entityClass = null,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([
            'fieldA' => $fieldA,
            'fieldB' => $fieldB,
            'message' => $message,
            'entityClass' => $entityClass
        ], $groups, $payload);
    }

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
