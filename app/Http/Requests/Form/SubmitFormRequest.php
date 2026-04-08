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

    protected function prepareForValidation(): void
    {
        $answers = $this->input('answers');

        if (is_string($answers)) {
            $decoded = json_decode($answers, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([
                    'answers' => $decoded,
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'respondent_name' => ['nullable', 'string', 'max:255'],
            'respondent_email' => ['nullable', 'email', 'max:255'],

            'answers' => ['required', 'array', 'min:1'],
            'answers.*.field_id' => ['required', 'integer', 'exists:form_fields,id'],
            'answers.*.value' => ['nullable'],

            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'mimes:pdf', 'max:5096'],
        ];
    }
}
