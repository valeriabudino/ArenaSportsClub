<?php

namespace App\Services;

use App\Models\Payment;
use Carbon\Carbon;

/**
 * Aplica al pago y al turno locales el estado que informa Mercado Pago.
 *
 * Como los turnos pendientes vencen (Turn::PAYMENT_WINDOW_MINUTES), el pago
 * puede llegar cuando el turno ya no es de quien pago. En ese caso no se
 * toca la reserva del otro usuario y el pago queda para reembolsar.
 */
class ReservationPaymentHandler
{
    /**
     * Estados a los que llega el pago por decisiones de la app (cancelacion o
     * reembolso). Mercado Pago puede repetir avisos viejos: si el pago ya esta
     * en uno de estos, se ignoran para no devolverle el turno a quien cancelo.
     */
    private const FINAL_STATUSES = ['refund_pending', 'refunded', 'cancelled_no_refund'];

    public function apply(Payment $payment, string $mpStatus, $mpPaymentId = null): void
    {
        if (in_array($payment->status, self::FINAL_STATUSES)) {
            return;
        }

        $turn = $payment->turn;
        $isPayersTurn = (int) $turn->user_id === (int) $payment->user_id
            && in_array($turn->status, ['pending_payment', 'booked']);

        if ($mpStatus === 'approved') {
            if ($isPayersTurn) {
                $turn->update(['status' => 'booked', 'reserved_until' => null]);
            } elseif ($turn->status === 'available' && ! $this->isPast($turn)) {
                // Se le vencio el plazo pero nadie lo tomo: se lo damos.
                $turn->update(['user_id' => $payment->user_id, 'status' => 'booked', 'reserved_until' => null]);
            } else {
                // El turno ya es de otro (o ya paso): hay que devolver la plata.
                $mpStatus = 'refund_pending';
            }
        } elseif (in_array($mpStatus, ['rejected', 'cancelled']) && $isPayersTurn && $turn->status === 'pending_payment') {
            $turn->update(['status' => 'available', 'user_id' => null]);
        }

        $payment->update(array_filter([
            'mp_payment_id' => $mpPaymentId,
            'status' => $mpStatus,
        ], fn ($value) => $value !== null));

        // Cada "Pagar" crea un pago pendiente; si uno se aprobo y el turno quedo
        // reservado, los demas del mismo turno ya no se van a usar.
        if ($mpStatus === 'approved') {
            Payment::where('turn_id', $turn->id)
                ->where('id', '!=', $payment->id)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);
        }
    }

    private function isPast($turn): bool
    {
        return Carbon::parse("{$turn->date} {$turn->start_time}")->isPast();
    }
}
