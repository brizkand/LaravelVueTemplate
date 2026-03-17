<?php

namespace App\Http\Requests;

use App\Models\System\Form\FormField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFieldRequest extends FormRequest
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
            'type' => ['required', Rule::in(FormField::TYPES)],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_required' => ['required', 'boolean'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'validation_rules' => ['nullable', 'array'],
            'is_active' => ['required', 'boolean'],
            'options' => ['nullable', 'array'],
            'options.*.label' => ['required_with:options', 'string', 'max:255'],
            'options.*.value' => ['required_with:options', 'string', 'max:255'],
            'options.*.sort_order' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
