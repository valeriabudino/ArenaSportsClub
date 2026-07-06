<?php

namespace App\Observers;

use App\Models\Turn;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Illuminate\Support\Facades\Log;
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

        $message = "¡Hola {$turn->user->name}! Tu reserva de {$turn->court->sport->name} en {$turn->court->name} quedó confirmada. Te estaremos mandando tu código QR de acceso más cerca del turno.";

        try {
            $sent = $this->whatsApp->sendMessage($turn->user->phone, $message);
        } catch (\Throwable $e) {
            $sent = false;
        }

        if (! $sent) {
            Log::warning('No se pudo enviar la confirmacion de reserva por WhatsApp', ['turn_id' => $turn->id]);
        }
    }
}
