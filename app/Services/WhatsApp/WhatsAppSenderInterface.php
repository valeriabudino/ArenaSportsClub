<?php

namespace App\Services\WhatsApp;

interface WhatsAppSenderInterface
{
    public function sendMessage(string $phoneNumber, string $message, ?string $mediaUrl = null): bool;
}
