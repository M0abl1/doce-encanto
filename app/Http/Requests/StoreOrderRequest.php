<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_name' => trim($this->text('customer_name')),
            'customer_phone' => preg_replace('/\D+/', '', $this->text('customer_phone')),
            'customer_email' => trim($this->text('customer_email')) ?: null,
            'notes' => trim($this->text('notes')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'min:3', 'max:100'],
            'customer_phone' => ['required', 'string', 'regex:/^\d{10,11}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'pickup_date' => [
                'required', 'date_format:Y-m-d',
                'after_or_equal:'.now()->addDays(2)->toDateString(),
                'before:'.now()->addMonths(3)->toDateString(),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
            'checkout_token' => ['required', 'string', 'regex:/^[A-Za-z0-9]{40}$/'],
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name' => 'nome',
            'customer_phone' => 'telefone',
            'customer_email' => 'e-mail',
            'pickup_date' => 'data de retirada',
            'notes' => 'observações',
            'checkout_token' => 'formulário',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Informe o telefone com DDD, ex.: (75) 99999-9999.',
            'pickup_date.after_or_equal' => 'As encomendas precisam de pelo menos 2 dias de antecedência.',
            'pickup_date.before' => 'Aceitamos encomendas para até 3 meses.',
            'checkout_token.*' => 'O formulário expirou. Recarregue a página e envie de novo.',
        ];
    }
}
