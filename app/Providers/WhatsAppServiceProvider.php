<?php

namespace App\Providers;

use App\Services\WhatsApp\TwilioWhatsAppSender;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

class WhatsAppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WhatsAppSenderInterface::class, function () {
            $config = config('services.twilio');

            return new TwilioWhatsAppSender(
                new Client($config['sid'], $config['token']),
                $config['whatsapp_from'],
            );
        });
    }
}
