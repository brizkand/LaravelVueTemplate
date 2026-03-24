<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'is_public' => ['required', 'boolean'],

            'fields' => ['required', 'array', 'min:1'],
            'fields.*.type' => [
                'required',
                'string',
                Rule::in([
                    'short_text',
                    'long_text',
                    'number',
                    'email',
                    'dropdown',
                    'radio',
                    'checkbox',
                    'date',
                    'rating',
                    'file',
                ]),
            ],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.description' => ['nullable', 'string'],
            'fields.*.is_required' => ['required', 'boolean'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.sort_order' => ['required', 'integer', 'min:1'],
            'fields.*.is_active' => ['required', 'boolean'],
            'fields.*.validation_rules' => ['nullable', 'array'],
            'fields.*.allow_other_option' => ['nullable', 'boolean'],
            'fields.*.other_option_label' => ['nullable', 'string', 'max:255'],

            'fields.*.options' => ['nullable', 'array'],
            'fields.*.options.*.label' => ['required_with:fields.*.options', 'string', 'max:255'],
            'fields.*.options.*.value' => ['required_with:fields.*.options', 'string', 'max:255'],
            'fields.*.options.*.sort_order' => ['required_with:fields.*.options', 'integer', 'min:1'],
        ];
    }
}
