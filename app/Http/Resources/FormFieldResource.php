<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldResource extends JsonResource
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
            'form_id' => $this->form_id,
            'type' => $this->type,
            'label' => $this->label,
            'description' => $this->description,
            'is_required' => $this->is_required,
            'placeholder' => $this->placeholder,
            'sort_order' => $this->sort_order,
            'validation_rules' => $this->validation_rules,
            'is_active' => $this->is_active,
            'options' => $this->options,
        ];
    }
}
