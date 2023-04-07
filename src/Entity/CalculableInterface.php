<?php

namespace App\Entity;

use App\Enum\TypeEnum;

interface CalculableInterface
{
    public function getAmount(): float;

    public function getType(): TypeEnum;

    public function isPast(): bool;
}
