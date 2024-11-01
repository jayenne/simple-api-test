<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Enums\PaymentEnum;

/**
 * create rule by which to validate the minimun amount payable
 */
class PaymentRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $min = PaymentEnum::MIN_VALUE->value();

        if (!ctype_digit($value) || $value < $min) {

            $fail(__('responses.minimum_payment_failure', ['attribute' => $attribute, 'rules' => 'an integer greater than', 'value' => $min]));
        }
    }
}
