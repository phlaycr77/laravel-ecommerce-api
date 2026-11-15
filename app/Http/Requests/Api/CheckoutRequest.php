<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_name'    => ['required', 'string', 'max:150'],
            'phone'            => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'note'             => ['nullable', 'string', 'max:500'],
        ];
    }
}