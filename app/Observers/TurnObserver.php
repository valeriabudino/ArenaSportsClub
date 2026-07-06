<?php

namespace App\Observers;

use App\Models\Turn;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class TurnObserver
{
    public function __construct(private readonly WhatsAppSenderInterface $whatsApp) {}

    public function updated(Turn $turn): void
    {
        if (! $turn->wasChanged('status') || $turn->status !== 'booked' || $turn->qr_code) {
            return;
        }

        $turn->qr_code = (string) Str::uuid();
        $turn->saveQuietly();

        $this->sendConfirmation($turn);
    }

    private function sendConfirmation(Turn $turn): void
    {
        if (! $turn->user || ! $turn->user->phone) {
            return;
        }

        $start = Carbon::parse("{$turn->date} {$turn->start_time}");
        $sendQrNow = now()->addHours(12)->greaterThanOrEqualTo($start);

        $message = "¡Hola {$turn->user->name}! Tu reserva de {$turn->court->sport->name} en {$turn->court->name} quedó confirmada."
            .($sendQrNow
                ? ' Te adjuntamos tu código QR de acceso, mostralo en la entrada.'
                : ' Te estaremos mandando tu código QR de acceso más cerca del turno.');

        $mediaUrl = $sendQrNow
            ? URL::temporarySignedRoute('reservations.qr.signed', now()->addHours(24), ['turn' => $turn->id])
            : null;

        try {
            $sent = $this->whatsApp->sendMessage($turn->user->phone, $message, $mediaUrl);
        } catch (\Throwable $e) {
            $sent = false;
        }

        if (! $sent) {
            Log::warning('No se pudo enviar la confirmacion de reserva por WhatsApp', ['turn_id' => $turn->id]);

            return;
        }

        if ($sendQrNow) {
            $turn->reminder_sent_at = now();
            $turn->saveQuietly();
        }
    }
}
