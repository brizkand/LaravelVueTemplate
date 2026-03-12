<?php

namespace App\Models\System;

use App\Models\System\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Profile extends Model
{
    protected $fillable = [
        'employee_number',
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'division_id',
        'section_id',
        'position_id'
    ];

    protected $appends = [
        'name',
        'avatar'
    ];

    public function scopeOfEmployeeNumber ($query, $employee_number) {
        return $query->where('employee_number', $employee_number);
    }

    public function getNameAttribute () : string {
        return "$this->first_name $this->last_name";
    }

    public function getReversenameAttribute () : string {
        return "$this->last_name $this->first_name";
    }

    public function getFullnameAttribute () : string {
        return $this->first_name . ' ' . strtoupper(substr($this->middle_name, 0, 1)) . '. '  . $this->last_name;
    }

    public function getAvatarAttribute () : array {
        $names = Str::of($this->name)->explode(' ');

        $fn = Str::of($names[0])->substr(0, 1)->upper();
        $ln = Str::of($names[1])->substr(0, 1)->upper();

        $colors = [
        'blue',
        'green',
        'yellow',
        'cyan',
        'red',
        'indigo',
        'teal',
        'orange',
        'bluegray',
        'purple'
        ];

        $color_index = (ord($fn) - 65) + (ord($ln) - 65);

        //$imageURL = "http://apps.pcieerd.dost.gov.ph/hrmis/images/employeeImages/{$this->employee_number}.jpg";

        //$image = @getimagesize($imageURL) ? $imageURL : null;

        return[
            'color' => $colors[$color_index % count($colors)],
            'text' => Str::of("$fn$ln"),
            //'image' => $image,
            'image' => null
        ];
    }

    public function user () {
        return $this->belongsTo(related: User::class);
    }
}
