<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = [
        'court_id',
        'date',
        'start_time',
        'end_time',
        'price',
        'status',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
