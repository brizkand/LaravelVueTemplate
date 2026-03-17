<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'answers' => ['required', 'array'],
            'answers.*.field_id' => ['required', 'integer', 'exists:form_fields,id'],
            'answers.*.value' => ['nullable'],
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
