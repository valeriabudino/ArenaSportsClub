<?php

namespace App\Rules;

use App\Support\ArgentinePhone;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida que el telefono se pueda convertir a un celular argentino para
 * WhatsApp. Despues de validar, guardarlo con ArgentinePhone::normalize().
 */
class ArgentineMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (ArgentinePhone::normalize((string) $value) === null) {
            $fail('Ingresá un celular con código de área, por ejemplo: 370 4123456.');
        }
    }
}
