<?php

namespace App\Models\Integration\HRMIS\v9;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $connection = 'hrmis';
    protected $table = 'tblPosition';
    protected $primaryKey = 'positionCode';
    protected $keyType = 'string';
    public $incrementing = false;
}
