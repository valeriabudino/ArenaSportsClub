<?php

namespace App\Observers;

use App\Models\Turn;
use Illuminate\Support\Str;

class TurnObserver
{
    public function updated(Turn $turn): void
    {
        if (! $turn->wasChanged('status') || $turn->status !== 'booked' || $turn->qr_code) {
            return;
        }

        $turn->qr_code = (string) Str::uuid();
        $turn->saveQuietly();
    }
}
