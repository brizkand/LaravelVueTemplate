<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFormRequest extends FormRequest
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
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_email' => ['nullable', 'email', 'max:255'],

            'answers' => ['required', 'array', 'min:1'],
            'answers.*.field_id' => ['required', 'integer', 'exists:form_fields,id'],
            'answers.*.value' => ['nullable'],
        ];
    }
}
