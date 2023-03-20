<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TwigPaymentExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPaymentExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paypalClientId', [TwigPaymentExtensionRuntime::class, 'getPaypalClientId']),
        ];
    }
}
