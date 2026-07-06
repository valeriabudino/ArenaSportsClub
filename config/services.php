<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mercadopago' => [
        'access_token' => env('MP_ACCESS_TOKEN'),
        'public_key' => env('MP_PUBLIC_KEY'),
        // Solo para desarrollo local: URL publica (tunel) donde Mercado Pago puede
        // notificar el webhook, ya que localhost no es alcanzable desde internet.
        'webhook_url' => env('MP_WEBHOOK_URL'),
    ],

    'twilio' => [
        // Account SID (AC...) y Auth Token, del Dashboard principal de la cuenta.
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_AUTH_TOKEN'),
        // Numero de WhatsApp habilitado en Twilio, formato E.164 (ej. +14155238886).
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],

    'canchas' => [
        // API del otro grupo (control de accesos fisico) que expone el reporte
        // diario de escaneos. La URL cambia cada vez que reinician su tunel.
        'daily_logs_url' => env('CANCHAS_API_URL'),
        'token' => env('CANCHAS_API_TOKEN'),
    ],

];
