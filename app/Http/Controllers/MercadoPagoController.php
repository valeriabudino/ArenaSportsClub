<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MercadoPago\Payment as MpPayment;
use MercadoPago\SDK;

class MercadoPagoController extends Controller
{
    public function webhook(Request $request)
    {
        $topic = $request->input('topic', $request->input('type'));
        $paymentId = $request->input('id') ?? data_get($request->input('data'), 'id') ?? $request->query('id');

        if ($topic !== 'payment' || ! $paymentId) {
            return response()->json(['ignored' => true]);
        }

        SDK::setAccessToken(config('services.mercadopago.access_token'));
        $mpPayment = MpPayment::find_by_id($paymentId);

        if (! $mpPayment || ! $mpPayment->external_reference) {
            return response()->json(['error' => 'payment not found'], 404);
        }

        $payment = Payment::find($mpPayment->external_reference);

        if (! $payment) {
            return response()->json(['error' => 'local payment record not found'], 404);
        }

        $payment->update([
            'mp_payment_id' => $mpPayment->id,
            'status' => $mpPayment->status,
        ]);

        $turn = $payment->turn;

        if ($mpPayment->status === 'approved') {
            $turn->update(['status' => 'booked', 'qr_code' => Str::uuid()]);
        } elseif (in_array($mpPayment->status, ['rejected', 'cancelled'])) {
            $turn->update(['status' => 'available', 'user_id' => null]);
        }

        return response()->json(['ok' => true]);
    }

    public function success()
    {
        return redirect()->route('reservations.index')->with('success', 'Pago aprobado. ¡Reserva confirmada!');
    }

    public function pending()
    {
        return redirect()->route('reservations.index')->with('success', 'Tu pago está pendiente de confirmación.');
    }

    public function failure()
    {
        return redirect()->route('reservations.index')->with('error', 'El pago no pudo procesarse. Intentá nuevamente.');
    }
}
