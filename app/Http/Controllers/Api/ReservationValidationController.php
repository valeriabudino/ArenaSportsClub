<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationValidationController extends Controller
{
    public function validate(Request $request)
    {
        $qrCode = $request->input('qr_code');

        $turn = $qrCode
            ? Turn::with('user')->where('qr_code', $qrCode)->where('status', 'booked')->first()
            : null;

        if (! $turn) {
            return $this->rejected();
        }

        $start = Carbon::parse("{$turn->date} {$turn->start_time}");
        $end = Carbon::parse("{$turn->date} {$turn->end_time}");
        $enabledFrom = $start->copy()->subMinutes(15);

        $now = Carbon::now();

        if ($now->lessThan($enabledFrom) || $now->greaterThan($end)) {
            return $this->rejected();
        }

        return response()->json([
            'valido' => true,
            'mensaje' => 'Reserva confirmada. Bienvenido.',
            'cliente' => $turn->user->name,
        ]);
    }

    private function rejected()
    {
        return response()->json([
            'valido' => false,
            'mensaje' => 'Reserva impaga o fuera de horario permitido.',
        ]);
    }
}
