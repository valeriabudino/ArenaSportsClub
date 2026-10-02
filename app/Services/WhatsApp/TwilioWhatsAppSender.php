<?php

namespace App\Services\WhatsApp;

use App\Support\ArgentinePhone;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioWhatsAppSender implements WhatsAppSenderInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly string $from,
    ) {}

    public function sendMessage(string $phoneNumber, string $message, ?string $mediaUrl = null): bool
    {
        // Se normaliza tambien aca para los telefonos guardados antes de que
        // el registro y el perfil los guardaran en formato internacional.
        $to = ArgentinePhone::normalize($phoneNumber);

        if ($to === null) {
            Log::warning('No se envio el WhatsApp: el telefono no es un celular argentino valido', ['phone' => $phoneNumber]);

            return false;
        }

        try {
            $params = ['from' => "whatsapp:{$this->from}", 'body' => $message];

            if ($mediaUrl) {
                $params['mediaUrl'] = [$mediaUrl];
            }

            $this->client->messages->create("whatsapp:{$to}", $params);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
