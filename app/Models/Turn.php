<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    protected $fillable = [
        'court_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'price',
        'status',
        'qr_code',
        'reminder_sent_at',
    ];

    protected $casts = [
        'reminder_sent_at' => 'datetime',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}