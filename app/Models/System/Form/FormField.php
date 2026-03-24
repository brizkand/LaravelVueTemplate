<?php

namespace App\Models\System\Form;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'type',
        'label',
        'description',
        'is_required',
        'placeholder',
        'sort_order',
        'validation_rules',
        'is_active',
        'allow_other_option',
        'other_option_label',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'allow_other_option' => 'boolean',
        'validation_rules' => 'array',
    ];

    public const TYPES = [
        'short_text',
        'long_text',
        'number',
        'email',
        'dropdown',
        'radio',
        'checkbox',
        'date',
        'rating',
        'file',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(FormFieldOption::class, 'field_id')->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FormAnswer::class, 'field_id');
    }
}
