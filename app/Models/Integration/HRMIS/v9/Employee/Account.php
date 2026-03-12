<?php

namespace App\Models\Integration\HRMIS\v9\Employee;

use App\Models\Integration\HRMIS\v9\Employee\Profile as HRMISProfile;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $connection = 'hrmis';
    protected $table = 'tblEmpAccount';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
        'empNumber',
        'userName',
        'userPassword',
        'userLevel',
        'userPermission',
        'assignedGroup',
        'signatory',
        'signatoryPosition',
        'admin',
        'dts_level',
        'is_assistant'
    ];

    protected $casts = [
        'admin' => 'boolean',
        'dts_level' => 'boolean',
        'is_assistant' => 'boolean'
    ];

    // Employee Profile
    public function profile () {
        return $this->belongsTo(
        HRMISProfile::class,
        'empNumber',
        'empNumber'
        );
    }
}
