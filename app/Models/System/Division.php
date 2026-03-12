<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    const PCMD = 1;
    const FAD = 2;
    const ETDD = 3;
    const EUSTDD = 4;
    const ITDD = 5;
    const RITDD = 6;
    const HRIDD = 7;
    const IGD = 8;
    const ODED = 9;
    const OED = 10;

    public function only($attributes)
    {
        return $this->only($attributes);
    }

    public function scopeOfCode ($query, $code) {
        return $query->where('code', $code);
    }

    public function sections () {
        return $this->hasMany(Section::class);
    }
}
