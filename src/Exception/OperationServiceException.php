<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class OperationServiceException extends CustomUserMessageAuthenticationException
{
    public function __construct(
        private readonly ConstraintViolationListInterface $errors,
        string $message = '',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
