<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    protected $fillable = [
        'sport_id',
        'name',
        'description',
        'price_per_hour',
        'capacity',
        'image',
        'is_active'
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function turns()
    {
        return $this->hasMany(Turn::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourtReview::class);
    }
}
