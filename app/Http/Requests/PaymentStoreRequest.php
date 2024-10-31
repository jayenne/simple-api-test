<?php

namespace App\Http\Requests;

use App\Rules\PaymentRule;
use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account' => ['required', 'integer', 'gt:0'], // This should handle more validation rules and return 422 on failure
            'amount' => ['required', 'integer', new PaymentRule],
        ];
    }
}
