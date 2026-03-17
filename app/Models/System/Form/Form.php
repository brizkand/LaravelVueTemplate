<?php

namespace App\Models\System\Form;

use App\Models\System\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'is_public',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FormResponse::class);
    }
}
