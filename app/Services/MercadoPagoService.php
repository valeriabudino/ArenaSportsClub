<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Turn;
use MercadoPago\Item;
use MercadoPago\Preference;
use MercadoPago\SDK;

class MercadoPagoService
{
    public function __construct()
    {
        SDK::setAccessToken(config('services.mercadopago.access_token'));
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

        $item = new Item();
        $item->title = "Turno {$turn->court->name} - {$turn->date} {$turn->start_time}";
        $item->quantity = 1;
        $item->unit_price = (float) $turn->price;
        $item->currency_id = 'ARS';

        $preference = new Preference();
        $preference->items = [$item];
        $preference->external_reference = (string) $payment->id;
        $preference->notification_url = config('services.mercadopago.webhook_url') ?: route('webhooks.mercadopago');
        $preference->back_urls = [
            'success' => route('payments.success'),
            'pending' => route('payments.pending'),
            'failure' => route('payments.failure'),
        ];
        // auto_return requiere back_urls publicas (https, no localhost); en local lo omitimos
        // y el usuario vuelve manualmente con el boton de Mercado Pago tras pagar.
        if (! str_contains(config('app.url'), 'localhost') && ! str_contains(config('app.url'), '127.0.0.1')) {
            $preference->auto_return = 'approved';
        }
        $preference->save();

        $payment->update(['mp_preference_id' => $preference->id]);

        return [
            'payment' => $payment,
            'checkout_url' => $preference->sandbox_init_point ?: $preference->init_point,
        ];
    }
}
