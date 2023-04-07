<?php

namespace App\Enum;

enum CalculationTypeEnum: int
{
    case SUBTRACTION = 1;
    case ADDITION = 2;
    case NOTHING = 3;
}
