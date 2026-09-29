<?php

namespace Tests\Unit;

use App\Support\ArgentinePhone;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArgentinePhoneTest extends TestCase
{
    public static function validNumbers(): array
    {
        return [
            // Formosa (area 370)
            'solo digitos' => ['3704123456', '+5493704123456'],
            'con espacios y guion' => ['370 412-3456', '+5493704123456'],
            'con 0 y 15' => ['0370 15 412 3456', '+5493704123456'],
            'con 15 sin 0' => ['370 15 4123456', '+5493704123456'],
            'con parentesis' => ['(0370) 15-412-3456', '+5493704123456'],
            'internacional sin 9' => ['+54 370 4123456', '+5493704123456'],
            'internacional con 9' => ['+54 9 370 412 3456', '+5493704123456'],
            'ya normalizado' => ['+5493704123456', '+5493704123456'],
            'sin + con 549' => ['5493704123456', '+5493704123456'],
            'con 00' => ['00 54 9 370 4123456', '+5493704123456'],
            'internacional con 15' => ['+54 370 15 4123456', '+5493704123456'],
            // Otras areas: 2, 3 y 4 digitos
            'Buenos Aires (11)' => ['011 15 2345-6789', '+5491123456789'],
            'Resistencia (362)' => ['0362 15 4123456', '+5493624123456'],
            'area de 4 digitos (3718)' => ['03718 15 423456', '+5493718423456'],
        ];
    }

    #[DataProvider('validNumbers')]
    public function test_it_normalizes_argentine_mobile_numbers(string $input, string $expected): void
    {
        $this->assertSame($expected, ArgentinePhone::normalize($input));
    }

    public static function invalidNumbers(): array
    {
        return [
            'vacio' => [''],
            'solo espacios' => ['   '],
            'sin codigo de area' => ['4123456'],
            'con 15 sin codigo de area' => ['15 4123456'],
            'le falta un digito' => ['370412345'],
            'le sobra un digito' => ['37041234567'],
            'de otro pais' => ['+34 612 345 678'],
            'letras' => ['mi celular'],
        ];
    }

    #[DataProvider('invalidNumbers')]
    public function test_it_rejects_numbers_it_cannot_normalize(string $input): void
    {
        $this->assertNull(ArgentinePhone::normalize($input));
    }

    public function test_null_stays_null(): void
    {
        $this->assertNull(ArgentinePhone::normalize(null));
    }
}
