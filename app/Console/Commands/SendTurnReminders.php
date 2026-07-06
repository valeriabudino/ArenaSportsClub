<?php

namespace App\Console\Commands;

use App\Models\Turn;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendTurnReminders extends Command
{
    protected $signature = 'turnos:recordatorio';

    protected $description = 'Envia por WhatsApp el recordatorio de turno 12hs antes, con el QR de acceso adjunto';

    public function handle(WhatsAppSenderInterface $sender): int
    {
        $windowStart = now()->addHours(11);
        $windowEnd = now()->addHours(12);

        $turns = Turn::with(['user', 'court.sport'])
            ->where('status', 'booked')
            ->whereNotNull('qr_code')
            ->whereNull('reminder_sent_at')
            ->whereIn('date', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->get()
            ->filter(function (Turn $turn) use ($windowStart, $windowEnd) {
                $start = Carbon::parse("{$turn->date} {$turn->start_time}");

                return $start->between($windowStart, $windowEnd);
            });

        $sent = 0;

        foreach ($turns as $turn) {
            if (! $turn->user || ! $turn->user->phone) {
                continue;
            }

            $start = Carbon::parse("{$turn->date} {$turn->start_time}");
            $message = "Hola {$turn->user->name}! Te recordamos tu turno de {$turn->court->sport->name} mañana a las {$start->format('H:i')}hs en {$turn->court->name}. Adjuntamos tu QR de acceso, mostralo en la entrada.";
            $qrUrl = URL::temporarySignedRoute('reservations.qr.signed', now()->addHours(24), ['turn' => $turn->id]);

            try {
                $success = $sender->sendMessage($turn->user->phone, $message, $qrUrl);
            } catch (\Throwable $e) {
                $success = false;
                Log::warning('Fallo el envio del recordatorio de turno por WhatsApp', [
                    'turn_id' => $turn->id,
                    'error' => $e->getMessage(),
                ]);
            }

            if (! $success) {
                Log::warning('El recordatorio de turno por WhatsApp no se pudo enviar', ['turn_id' => $turn->id]);
                continue;
            }

            $turn->update(['reminder_sent_at' => now()]);
            $sent++;
        }

        $this->info("Recordatorios enviados: {$sent}");

        return self::SUCCESS;
    }
}
