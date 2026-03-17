<?php

namespace App\Models\System\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormAnswer extends Model
{
    protected $fillable = [
        'response_id',
        'field_id',
        'value_text',
        'value_number',
        'value_date',
        'value_email',
        'value_file_path',
        'value_json',
    ];

    protected $casts = [
        'value_date' => 'date',
        'value_json' => 'array',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(FormResponse::class, 'response_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }

    public function getNormalizedValueAttribute(): mixed
    {
        return $this->value_text
            ?? $this->value_number
            ?? $this->value_date
            ?? $this->value_email
            ?? $this->value_file_path
            ?? $this->value_json;
    }
}
