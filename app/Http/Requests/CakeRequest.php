<?php

namespace App\Http\Requests;

use App\Support\Money;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $price = $this->input('price');
        $name = $this->input('name');

        $this->merge([
            'price_cents' => is_string($price) ? Money::parse($price) : null,
            'slug' => is_string($name) ? Str::slug($name) : '',
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $cake = $this->route('cake');

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'slug' => ['required', 'string', 'max:120', Rule::unique('cakes', 'slug')->ignore($cake?->id)],
            'summary' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'string', 'max:20'],
            'price_cents' => ['required', 'integer', 'min:100', 'max:10000000'],
            'serves' => ['nullable', 'string', 'max:40'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'remove_image' => ['nullable', 'boolean'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'categoria', 'name' => 'nome', 'summary' => 'resumo', 'description' => 'descrição',
            'price' => 'preço', 'price_cents' => 'preço', 'serves' => 'rendimento', 'color' => 'cor', 'image' => 'foto',
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'Já existe um bolo com esse nome.',
            'price_cents.required' => 'Informe o preço no formato 89,90.',
            'price_cents.min' => 'O preço mínimo é R$ 1,00.',
            'price_cents.max' => 'O preço máximo é R$ 100.000,00.',
        ];
    }

    /** Campos que vão para o banco (sem o preço em texto nem o arquivo). */
    public function cakeData(): array
    {
        return collect($this->validated())->except(['price', 'image', 'remove_image'])->all();
    }
}
