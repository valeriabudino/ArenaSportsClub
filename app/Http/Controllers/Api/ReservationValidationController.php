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
        $qrCode = $request->query('qr_code');

        $turn = $qrCode
            ? Turn::with('court')->where('qr_code', $qrCode)->where('status', 'booked')->first()
            : null;

        if (! $turn) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error: Reserva no encontrada o fuera de horario',
            ], 404);
        }

        $start = Carbon::parse("{$turn->date} {$turn->start_time}");
        $end = Carbon::parse("{$turn->date} {$turn->end_time}");
        $enabledFrom = $start->copy()->subMinutes(15);
        $enabledUntil = $end->copy()->subMinutes(15);

        $now = Carbon::now();

        if ($now->lessThan($enabledFrom) || $now->greaterThan($enabledUntil)) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Error: Reserva no encontrada o fuera de horario',
            ], 404);
        }

        return response()->json([
            'valido' => true,
            'mensaje' => "Reserva confirmada: {$turn->court->name} - {$start->format('H:i')}hs",
        ]);
    }
}
