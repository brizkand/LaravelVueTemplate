<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Form\SubmitFormRequest;
use App\Models\System\Form\Form;
use App\Models\System\Form\FormAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormSubmissionController extends Controller
{
    public function show(Form $form, Request $request)
    {
        $user = auth('sanctum')->user();

        if (! $form->is_active) {
            return response()->json([
                'message' => 'This form is inactive.',
            ], 403);
        }

        if (! $form->is_public && ! $user) {
            return response()->json([
                'message' => 'This form requires authentication.',
            ], 401);
        }

        $form->load(['fields.options']);

        return response()->json([
            'data' => [
                'id' => $form->id,
                'title' => $form->title,
                'description' => $form->description,
                'is_active' => $form->is_active,
                'is_public' => $form->is_public,
                'fields' => $form->fields
                    ->where('is_active', true)
                    ->sortBy('sort_order')
                    ->values()
                    ->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'type' => $field->type,
                            'label' => $field->label,
                            'description' => $field->description,
                            'is_required' => $field->is_required,
                            'placeholder' => $field->placeholder,
                            'sort_order' => $field->sort_order,
                            'validation_rules' => $field->validation_rules,
                            'allow_other_option' => $field->allow_other_option,
                            'other_option_label' => $field->other_option_label,
                            'options' => $field->options
                                ->sortBy('sort_order')
                                ->values()
                                ->map(function ($option) {
                                    return [
                                        'id' => $option->id,
                                        'label' => $option->label,
                                        'value' => $option->value,
                                        'sort_order' => $option->sort_order,
                                    ];
                                }),
                        ];
                    }),
            ],
        ]);
    }

    public function store(SubmitFormRequest $request, Form $form)
    {
        $user = auth('sanctum')->user();

        if (! $form->is_active) {
            return response()->json([
                'message' => 'This form is inactive.',
            ], 403);
        }

        if (! $form->is_public && ! $user) {
            return response()->json([
                'message' => 'This form requires authentication.',
            ], 401);
        }

        $form->load(['fields.options']);

        $activeFields = $form->fields->where('is_active', true)->keyBy('id');

        foreach ($request->input('answers', []) as $answerData) {
            $fieldId = (int) $answerData['field_id'];

            if (! $activeFields->has($fieldId)) {
                return response()->json([
                    'message' => 'One or more submitted fields are invalid for this form.',
                ], 422);
            }
        }

        foreach ($activeFields as $field) {
            $submitted = collect($request->input('answers', []))->firstWhere('field_id', $field->id);
            $rawValue = $submitted['value'] ?? null;
            $normalized = $this->normalizeAnswerValue($field, $rawValue);

            if ($field->is_required) {
                if ($field->type === 'checkbox') {
                    if (empty($normalized['selected'])) {
                        return response()->json([
                            'message' => "The field '{$field->label}' is required.",
                        ], 422);
                    }
                } elseif ($field->type === 'rating') {
                    if ($normalized['value'] === null || (int) $normalized['value'] < 1) {
                        return response()->json([
                            'message' => "The field '{$field->label}' is required.",
                        ], 422);
                    }
                } else {
                    if ($normalized['value'] === null || $normalized['value'] === '') {
                        return response()->json([
                            'message' => "The field '{$field->label}' is required.",
                        ], 422);
                    }
                }
            }
        }

        $response = DB::transaction(function () use ($request, $form, $activeFields, $user) {
            $response = $form->responses()->create([
                'submitted_by' => $user?->id,
                'reference_code' => 'FRM-' . strtoupper(Str::random(10)),
                'respondent_name' => $request->input('respondent_name'),
                'respondent_email' => $request->input('respondent_email'),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 65535),
                'submitted_at' => now(),
            ]);

            foreach ($request->input('answers', []) as $answerData) {
                $field = $activeFields->get((int) $answerData['field_id']);
                $rawValue = $answerData['value'] ?? null;
                $normalized = $this->normalizeAnswerValue($field, $rawValue);

                $payload = [
                    'response_id' => $response->id,
                    'field_id' => $field->id,
                    'value_text' => null,
                    'value_number' => null,
                    'value_date' => null,
                    'value_email' => null,
                    'value_file_path' => null,
                    'value_json' => null,
                ];

                switch ($field->type) {
                    case 'number':
                    case 'rating':
                        $payload['value_number'] = ($normalized['value'] === '' || $normalized['value'] === null)
                            ? null
                            : $normalized['value'];
                        break;

                    case 'email':
                        $payload['value_email'] = ($normalized['value'] === '' || $normalized['value'] === null)
                            ? null
                            : $normalized['value'];
                        break;

                    case 'date':
                        $payload['value_date'] = ($normalized['value'] === '' || $normalized['value'] === null)
                            ? null
                            : $normalized['value'];
                        break;

                    case 'checkbox':
                        $payload['value_json'] = [
                            'selected' => $normalized['selected'],
                            'other_text' => $normalized['other_text'],
                        ];
                        break;

                    case 'radio':
                    case 'dropdown':
                        if ($field->allow_other_option && $normalized['selected'] === '__other__') {
                            $payload['value_json'] = [
                                'selected' => '__other__',
                                'other_text' => $normalized['other_text'],
                            ];
                        } else {
                            $payload['value_text'] = ($normalized['value'] === '' || $normalized['value'] === null)
                                ? null
                                : $normalized['value'];
                        }
                        break;

                    case 'file':
                        $payload['value_file_path'] = ($normalized['value'] === '' || $normalized['value'] === null)
                            ? null
                            : $normalized['value'];
                        break;

                    default:
                        $payload['value_text'] = ($normalized['value'] === '' || $normalized['value'] === null)
                            ? null
                            : $normalized['value'];
                        break;
                }

                FormAnswer::create($payload);
            }

            return $response;
        });

        return response()->json([
            'message' => 'Form submitted successfully.',
            'data' => [
                'id' => $response->id,
                'reference_code' => $response->reference_code,
                'submitted_at' => $response->submitted_at,
            ],
        ], 201);
    }

    private function normalizeAnswerValue($field, $rawValue): array
    {
        $result = [
            'value' => $rawValue,
            'selected' => null,
            'other_text' => null,
        ];

        if ($field->type === 'radio' || $field->type === 'dropdown') {
            if (is_array($rawValue)) {
                $result['selected'] = $rawValue['selected'] ?? null;
                $result['other_text'] = $rawValue['other_text'] ?? null;
                $result['value'] = $rawValue['selected'] ?? null;
            } else {
                $result['selected'] = $rawValue;
            }
        }

        if ($field->type === 'checkbox') {
            if (is_array($rawValue) && array_key_exists('selected', $rawValue)) {
                $result['selected'] = is_array($rawValue['selected']) ? array_values($rawValue['selected']) : [];
                $result['other_text'] = $rawValue['other_text'] ?? null;
                $result['value'] = $result['selected'];
            } else {
                $result['selected'] = is_array($rawValue) ? array_values($rawValue) : [];
                $result['value'] = $result['selected'];
            }
        }

        return $result;
    }
}
