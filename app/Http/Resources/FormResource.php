<?php

namespace App\Http\Resources;

use App\Http\Resources\FormFieldResource;
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
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'created_by' => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ],
            'fields_count' => $this->whenCounted('fields'),
            'responses_count' => $this->whenCounted('responses'),
            'fields' => FormFieldResource::collection($this->whenLoaded('fields')),
        ];
    }
}
