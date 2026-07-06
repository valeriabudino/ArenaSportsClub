<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = [
        'date',
        'scanned_at',
        'qr_code',
        'status',
        'details',
    ];

    protected $casts = [
        'date' => 'date',
        'scanned_at' => 'datetime',
    ];
}
