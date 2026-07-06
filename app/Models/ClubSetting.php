<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubSetting extends Model
{
    protected $fillable = [
        'name',
        'description',
        'email',
        'phone',
        'address',
        'opening_time',
        'closing_time',
        'instagram',
        'facebook',
    ];
}