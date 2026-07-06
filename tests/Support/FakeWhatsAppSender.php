<?php

namespace Tests\Support;

use App\Services\WhatsApp\WhatsAppSenderInterface;

class FakeWhatsAppSender implements WhatsAppSenderInterface
{
    public array $sent = [];

    public function __construct(private readonly bool $succeeds = true) {}

    public function sendMessage(string $phoneNumber, string $message, ?string $mediaUrl = null): bool
    {
        $this->sent[] = ['phone' => $phoneNumber, 'message' => $message, 'mediaUrl' => $mediaUrl];

        return $this->succeeds;
    }
}
