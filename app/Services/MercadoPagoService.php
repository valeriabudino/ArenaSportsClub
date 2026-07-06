<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Turn;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(
            config('services.mercadopago.access_token')
        );
    }

    /**
     * Crea el registro de pago local y la preferencia de Checkout Pro en Mercado Pago.
     *
     * @return array{payment: Payment, checkout_url: string}
     */
    public function createPreference(Turn $turn): array
    {
        $payment = Payment::create([
            'turn_id' => $turn->id,
            'user_id' => $turn->user_id,
            'amount' => $turn->price,
            'method' => 'mp',
            'status' => 'pending',
        ]);

        $client = new PreferenceClient();

$preference = $client->create([
    "items" => [
        [
            "title" => "Turno {$turn->court->name} - {$turn->date} {$turn->start_time}",
            "quantity" => 1,
            "unit_price" => (float) $turn->price,
            "currency_id" => "ARS",
        ]
    ],

    "external_reference" => (string) $payment->id,

    "notification_url" =>
        config('services.mercadopago.webhook_url')
        ?: route('webhooks.mercadopago'),

    "back_urls" => [
        "success" => route('payments.success'),
        "pending" => route('payments.pending'),
        "failure" => route('payments.failure'),
    ],
]);

        $payment->update(['mp_preference_id' => $preference->id]);

        return [
            'payment' => $payment,
            // sandbox_init_point es un dominio legacy que Mercado Pago dejo de
            // sostener; init_point ya sirve la experiencia de prueba o de
            // produccion segun las credenciales usadas para crear la preferencia.
            'checkout_url' => $preference->init_point,
        ];
    }
}
