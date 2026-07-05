<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'turn_id',
        'user_id',
        'amount',
        'method',
        'status',
        'mp_preference_id',
        'mp_payment_id',
    ];

    public function turn()
    {
        return $this->belongsTo(Turn::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
