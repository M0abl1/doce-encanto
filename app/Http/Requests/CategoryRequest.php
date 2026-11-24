<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->text('name'))]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:60'],
            'slug' => ['required', 'string', 'max:80', Rule::unique('categories', 'slug')->ignore($this->route('category')?->id)],
            'position' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nome', 'position' => 'ordem'];
    }

    public function messages(): array
    {
        return ['slug.unique' => 'Já existe uma categoria com esse nome.'];
    }
}
