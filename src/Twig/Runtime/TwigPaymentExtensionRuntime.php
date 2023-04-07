<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class TwigPaymentExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly string $paypalClientId = ''
    ) {
    }

    public function getPaypalClientId(): string
    {
        return $this->paypalClientId;
    }
}
