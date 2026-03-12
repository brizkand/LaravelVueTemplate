<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public function scopeOfCode ($query, $code) {
        return $query->where('code', $code);
    }
}
