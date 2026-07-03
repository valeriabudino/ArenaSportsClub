<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'default_start_time',
        'default_end_time',
        'slot_duration_minutes',
    ];

    public function courts()
    {
        return $this->hasMany(Court::class);
    }
}
