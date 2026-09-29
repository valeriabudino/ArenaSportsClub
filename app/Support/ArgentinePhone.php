<?php

namespace App\Support;

/**
 * Normaliza celulares argentinos al formato que exige WhatsApp/Twilio
 * (E.164 con el 9 de celular): +549 + codigo de area + numero.
 *
 * Acepta como los escribe la gente: con espacios o guiones, con el 0 del
 * codigo de area, con el 15, con o sin +54 / +549. Codigo de area + numero
 * siempre suman 10 digitos en Argentina.
 *
 *   ArgentinePhone::normalize('0370 15 412-3456')  // '+5493704123456'
 *   ArgentinePhone::normalize('4123456')           // null (falta el codigo de area)
 */
class ArgentinePhone
{
    public static function normalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        // 00 es el prefijo internacional marcando desde un telefono fijo.
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (! str_starts_with(trim($value), '+') && ! (strlen($digits) >= 12 && str_starts_with($digits, '54'))) {
            $digits = '54' . $digits;
        }

        // Solo celulares argentinos.
        if (! str_starts_with($digits, '54')) {
            return null;
        }

        $digits = substr($digits, 2);

        if (str_starts_with($digits, '9')) {
            $digits = substr($digits, 1);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        // Con el 15 quedan 12 digitos; el codigo de area tiene 2, 3 o 4.
        if (strlen($digits) === 12) {
            foreach ([2, 3, 4] as $areaLength) {
                if (substr($digits, $areaLength, 2) === '15') {
                    $digits = substr($digits, 0, $areaLength) . substr($digits, $areaLength + 2);
                    break;
                }
            }
        }

        return strlen($digits) === 10 ? '+549' . $digits : null;
    }
}
