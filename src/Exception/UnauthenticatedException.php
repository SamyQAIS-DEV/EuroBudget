<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UnauthenticatedException extends CustomUserMessageAuthenticationException
{
    public function __construct(
        string $message = 'Vous devez être connecté afin d\'accéder à cette ressource.',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
