<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalInformation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'description',
        'facilities',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
        ];
    }
}
