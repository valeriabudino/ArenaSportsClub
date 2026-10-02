<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Lee los avisos que manda Mercado Pago al webhook. Llegan en dos formatos:
 *
 * - IPN (viejo):       ?topic=payment&id=<id del pago>
 * - Webhooks (nuevo):  ?type=payment&data.id=<id del pago>
 *                      body {"id": <id del AVISO>, "type": "payment", "data": {"id": "<id del pago>"}}
 *
 * En el formato nuevo "id" es el numero del aviso, no el del pago: por eso
 * se busca primero data.id. (PHP convierte "data.id" de la URL en "data_id".)
 */
class MercadoPagoNotification
{
    public static function isPayment(Request $request): bool
    {
        return ($request->input('topic') ?? $request->input('type')) === 'payment';
    }

    public static function paymentId(Request $request): ?string
    {
        $id = $request->input('data.id') ?? $request->input('data_id');

        // Solo en el formato IPN el "id" suelto es el del pago.
        if ($id === null && $request->has('topic')) {
            $id = $request->input('id');
        }

        return $id === null || $id === '' ? null : (string) $id;
    }
}
