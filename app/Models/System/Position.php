<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    public function scopeOfCode($query, $code) {
        return $query->whereCode($code);
    }
}
