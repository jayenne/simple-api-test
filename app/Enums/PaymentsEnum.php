<?php

namespace App\Enums;

/**
 * set the set amounts that can be deposited
 */
enum PaymentsEnum: int
{
    case DEFAULT_VALUE    = 0;
    case MIN_VALUE        = 1;

    public function value(): int
    {
        return $this->value;
    }
}
