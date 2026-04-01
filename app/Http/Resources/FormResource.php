<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'is_public' => $this->is_public,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'created_by' => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->profile?->name ?? $this->creator?->email ?? 'Unknown User',
            ],

            'fields_count' => $this->whenCounted('fields'),
            'responses_count' => $this->whenCounted('responses'),

            'fields' => $this->whenLoaded('fields', function () {
                return $this->fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'type' => $this->normalizeFieldType($field->type),
                        'label' => $field->label,
                        'description' => $field->description,
                        'is_required' => $field->is_required,
                        'placeholder' => $field->placeholder,
                        'sort_order' => $field->sort_order,
                        'is_active' => $field->is_active,
                        'validation_rules' => $field->validation_rules,
                        'field_settings' => $field->field_settings,
                        'allow_other_option' => $field->allow_other_option,
                        'other_option_label' => $field->other_option_label,
                        'options' => $field->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'label' => $option->label,
                                'value' => $option->value,
                                'sort_order' => $option->sort_order,
                            ];
                        })->values(),
                    ];
                })->values();
            }),
        ];
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
