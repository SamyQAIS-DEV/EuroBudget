<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class EntityNotFoundException extends CustomUserMessageAuthenticationException
{
    public function __construct(
        string $message = 'La ressource nécessaire n\'a pas été trouvée.',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
