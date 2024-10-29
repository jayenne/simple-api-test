<?php

namespace App\Enums;

/**
 * set the set values of the http errors
 */
enum HttpStatusEnum: string
{
    case SUCCESS    = 'success';
    case ERROR      = 'error';

    public function value(): string
    {
        return $this->value;
    }
}
