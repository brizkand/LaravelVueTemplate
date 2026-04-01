<?php

namespace App\Http\Requests\Form;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;



class UpdateFormRequest extends FormRequest
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
        $fields = collect($this->input('fields', []))
            ->map(function ($field) {
                $type = $field['type'] ?? null;

                $field['type'] = $this->normalizeFieldType($type);

                return $field;
            })
            ->values()
            ->all();

        $this->merge([
            'fields' => $fields,
        ]);
    }

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
                    'time',
                    'rating',
                    'linear_scale',
                    'multiple_choice_grid',
                    'checkbox_grid',
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
            'fields.*.validation_rules.min' => ['nullable', 'numeric'],
            'fields.*.validation_rules.max' => ['nullable', 'numeric'],
            'fields.*.validation_rules.max_size_kb' => ['nullable', 'integer', 'min:1'],
            'fields.*.validation_rules.allowed_types' => ['nullable', 'array'],
            'fields.*.validation_rules.allowed_types.*' => ['nullable', 'string', 'max:50'],

            'fields.*.field_settings' => ['nullable', 'array'],
            'fields.*.field_settings.scale_start' => ['nullable', 'integer'],
            'fields.*.field_settings.scale_end' => ['nullable', 'integer'],
            'fields.*.field_settings.start_label' => ['nullable', 'string', 'max:255'],
            'fields.*.field_settings.end_label' => ['nullable', 'string', 'max:255'],

            'fields.*.field_settings.rows' => ['nullable', 'array'],
            'fields.*.field_settings.rows.*.label' => ['required_with:fields.*.field_settings.rows', 'string', 'max:255'],
            'fields.*.field_settings.rows.*.value' => ['required_with:fields.*.field_settings.rows', 'string', 'max:255'],
            'fields.*.field_settings.rows.*.sort_order' => ['required_with:fields.*.field_settings.rows', 'integer', 'min:1'],

            'fields.*.field_settings.columns' => ['nullable', 'array'],
            'fields.*.field_settings.columns.*.label' => ['required_with:fields.*.field_settings.columns', 'string', 'max:255'],
            'fields.*.field_settings.columns.*.value' => ['required_with:fields.*.field_settings.columns', 'string', 'max:255'],
            'fields.*.field_settings.columns.*.sort_order' => ['required_with:fields.*.field_settings.columns', 'integer', 'min:1'],

            'fields.*.options' => ['nullable', 'array'],
            'fields.*.options.*.label' => ['required_with:fields.*.options', 'string', 'max:255'],
            'fields.*.options.*.value' => ['required_with:fields.*.options', 'string', 'max:255'],
            'fields.*.options.*.sort_order' => ['required_with:fields.*.options', 'integer', 'min:1'],

            'fields.*.allow_other_option' => ['nullable', 'boolean'],
            'fields.*.other_option_label' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'form title',
            'description' => 'form description',
            'fields' => 'form fields',
            'fields.*.label' => 'question label',
            'fields.*.type' => 'question type',
            'fields.*.options' => 'question options',
            'fields.*.field_settings.rows' => 'grid rows',
            'fields.*.field_settings.columns' => 'grid columns',
            'fields.*.field_settings.start_label' => 'linear scale start label',
            'fields.*.field_settings.end_label' => 'linear scale end label',
        ];
    }

    public function messages(): array
    {
        return [
            'fields.required' => 'At least one question is required.',
            'fields.min' => 'At least one question is required.',
            'fields.*.type.in' => 'The selected question type is invalid.',
            'fields.*.label.required' => 'Each question must have a label.',
            'fields.*.options.*.label.required_with' => 'Each option must have a label.',
            'fields.*.options.*.value.required_with' => 'Each option must have a value.',
            'fields.*.field_settings.rows.*.label.required_with' => 'Each grid row must have a label.',
            'fields.*.field_settings.rows.*.value.required_with' => 'Each grid row must have a value.',
            'fields.*.field_settings.columns.*.label.required_with' => 'Each grid column must have a label.',
            'fields.*.field_settings.columns.*.value.required_with' => 'Each grid column must have a value.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fields = $this->input('fields', []);

            foreach ($fields as $index => $field) {
                $type = $field['type'] ?? null;
                $options = $field['options'] ?? [];
                $settings = $field['field_settings'] ?? [];
                $allowOther = (bool) ($field['allow_other_option'] ?? false);
                $otherOptionLabel = trim((string) ($field['other_option_label'] ?? ''));

                if (in_array($type, ['dropdown', 'radio', 'checkbox'], true) && count($options) === 0) {
                    $validator->errors()->add("fields.$index.options", 'This question type requires at least one option.');
                }

                if (in_array($type, ['radio', 'checkbox'], true) && $allowOther && $otherOptionLabel === '') {
                    $validator->errors()->add("fields.$index.other_option_label", 'The other option label is required when "Allow Other option" is enabled.');
                }

                if ($type === 'linear_scale') {
                    $scaleStart = $settings['scale_start'] ?? null;
                    $scaleEnd = $settings['scale_end'] ?? null;
                    $startLabel = trim((string) ($settings['start_label'] ?? ''));
                    $endLabel = trim((string) ($settings['end_label'] ?? ''));

                    if ($scaleStart === null || $scaleEnd === null) {
                        $validator->errors()->add("fields.$index.field_settings.scale_start", 'Linear scale start and end values are required.');
                    }

                    if ($scaleStart !== null && $scaleEnd !== null && (int) $scaleStart >= (int) $scaleEnd) {
                        $validator->errors()->add("fields.$index.field_settings.scale_end", 'Linear scale end value must be greater than start value.');
                    }

                    if ($startLabel === '') {
                        $validator->errors()->add("fields.$index.field_settings.start_label", 'Linear scale start label is required.');
                    }

                    if ($endLabel === '') {
                        $validator->errors()->add("fields.$index.field_settings.end_label", 'Linear scale end label is required.');
                    }
                }

                if (in_array($type, ['multiple_choice_grid', 'checkbox_grid'], true)) {
                    $rows = $settings['rows'] ?? [];
                    $columns = $settings['columns'] ?? [];

                    if (!is_array($rows) || count($rows) === 0) {
                        $validator->errors()->add("fields.$index.field_settings.rows", 'This grid question requires at least one row.');
                    }

                    if (!is_array($columns) || count($columns) === 0) {
                        $validator->errors()->add("fields.$index.field_settings.columns", 'This grid question requires at least one column.');
                    }
                }

                if (in_array($type, ['number', 'rating'], true)) {
                    $min = data_get($field, 'validation_rules.min');
                    $max = data_get($field, 'validation_rules.max');

                    if ($min !== null && $max !== null && is_numeric($min) && is_numeric($max) && $min > $max) {
                        $validator->errors()->add("fields.$index.validation_rules.max", 'Maximum value must be greater than or equal to minimum value.');
                    }
                }

                if ($type === 'file' && count($options) > 0) {
                    $validator->errors()->add("fields.$index.options", 'File upload questions must not contain options.');
                }

                if (in_array($type, ['multiple_choice_grid', 'checkbox_grid', 'linear_scale'], true) && count($options) > 0) {
                    $validator->errors()->add("fields.$index.options", 'This question type must use field settings instead of simple options.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    private function normalizeFieldType(?string $type): ?string
    {
        return match ($type) {
            'multiple_choice' => 'radio',
            'multiple-choice' => 'radio',
            'shorttext' => 'short_text',
            'longtext' => 'long_text',
            'file_upload' => 'file',
            'linearScale' => 'linear_scale',
            'multiple_choice_grid_question' => 'multiple_choice_grid',
            'checkbox_grid_question' => 'checkbox_grid',
            default => $type,
        };
    }
}
