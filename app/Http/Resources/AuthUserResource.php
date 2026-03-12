<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "email"=> $this->email,
            "avatar_name_initials"=>
                strtoupper(substr($this->profile->first_name ?? '', 0, 1) .
                substr($this->profile->last_name ?? '', 0, 1)),
        ];
    }
}
