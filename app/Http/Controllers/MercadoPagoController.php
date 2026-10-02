<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\ReservationPaymentHandler;
use App\Support\MercadoPagoNotification;
use Illuminate\Http\Request;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoController extends Controller
{
    public function webhook(Request $request)
    {
        $paymentId = MercadoPagoNotification::paymentId($request);

        if (! MercadoPagoNotification::isPayment($request) || ! $paymentId) {
            return response()->json(['ignored' => true]);
        }

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

        try {
            $mpPayment = (new PaymentClient())->get((int) $paymentId);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'payment not found'], 404);
        }

        if (! $mpPayment || ! $mpPayment->external_reference) {
            return response()->json(['error' => 'payment not found'], 404);
        }

        $payment = Payment::find($mpPayment->external_reference);

        if (! $payment) {
            return response()->json(['error' => 'local payment record not found'], 404);
        }

        app(ReservationPaymentHandler::class)->apply($payment, $mpPayment->status, $mpPayment->id);

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
