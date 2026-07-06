# Colección Postman — Validación de QR

Importá `ArenaSportClub-QR-Validation.postman_collection.json` en Postman (File → Import).

Los `qr_code_*` cargados en las variables de la colección son de turnos de prueba en tu base local — si los borrás o reseteás la DB, vas a tener que regenerarlos y actualizar las variables de la colección (Collection → Variables).

## Regenerar los turnos de prueba

Con `php artisan serve` corriendo, en otra terminal:

```
php artisan tinker
```

```php
$court = App\Models\Court::first();

// Aprobado: booked, dentro de la ventana horaria ahora mismo
$aprobado = App\Models\Turn::create([
    'court_id' => $court->id,
    'date' => now()->toDateString(),
    'start_time' => now()->format('H:i:s'),
    'end_time' => now()->addHour()->format('H:i:s'),
    'price' => 5000,
    'status' => 'available',
]);
$aprobado->update(['status' => 'booked']); // dispara el Observer, genera qr_code

// Impago: pending_payment con qr_code a mano (solo para poder testear el rechazo)
$impago = App\Models\Turn::create([
    'court_id' => $court->id,
    'date' => now()->toDateString(),
    'start_time' => now()->format('H:i:s'),
    'end_time' => now()->addHour()->format('H:i:s'),
    'price' => 5000,
    'status' => 'pending_payment',
    'qr_code' => (string) Illuminate\Support\Str::uuid(),
]);

// Vencido: booked pero ya termino
$vencido = App\Models\Turn::create([
    'court_id' => $court->id,
    'date' => now()->toDateString(),
    'start_time' => now()->subHours(3)->format('H:i:s'),
    'end_time' => now()->subHours(2)->format('H:i:s'),
    'price' => 5000,
    'status' => 'available',
]);
$vencido->update(['status' => 'booked']);

echo $aprobado->fresh()->qr_code;
echo $impago->qr_code;
echo $vencido->fresh()->qr_code;
```

Pegá esos tres valores en las variables `qr_code_aprobado`, `qr_code_impago` y `qr_code_vencido` de la colección.
