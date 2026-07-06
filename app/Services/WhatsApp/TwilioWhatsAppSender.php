<?php

namespace App\Services\WhatsApp;

use Twilio\Rest\Client;

class TwilioWhatsAppSender implements WhatsAppSenderInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly string $from,
    ) {}

    public function sendMessage(string $phoneNumber, string $message, ?string $mediaUrl = null): bool
    {
        try {
            $params = ['from' => "whatsapp:{$this->from}", 'body' => $message];

            if ($mediaUrl) {
                $params['mediaUrl'] = [$mediaUrl];
            }

            $this->client->messages->create("whatsapp:{$phoneNumber}", $params);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
