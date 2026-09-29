<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\CourtReview;
use App\Models\Payment;
use App\Models\Turn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Datos de demostracion: clientes ficticios con reservas pasadas pagadas y
 * comentarios, para que la app se vea en uso (estadisticas del inicio,
 * dashboard, turnos, comentarios de cada cancha).
 *
 *   php artisan db:seed --class=DemoDataSeeder   (cargar)
 *   php artisan demo:borrar                      (sacarlos)
 *
 * - Los clientes usan emails @demo.example.com (dominio reservado, no
 *   existe) y no tienen telefono: no se manda ningun mail ni WhatsApp.
 * - Las reservas son del pasado, asi no ocupan horarios para reservar.
 * - Cada comentario es de un cliente que jugo en esa cancha (misma regla
 *   que exige la app para comentar).
 * - Usa una semilla fija: siempre genera los mismos datos.
 */
class DemoDataSeeder extends Seeder
{
    public const EMAIL_DOMAIN = 'demo.example.com';

    private const BOOKINGS = 40;

    private const REVIEWS = 15;

    private const CLIENTS = [
        'Lucía Fernández', 'Martín Gómez', 'Sofía Romero', 'Tomás Acosta',
        'Valentina Ruiz', 'Nicolás Sosa', 'Camila Benítez', 'Joaquín Medina',
        'Julieta Herrera', 'Franco Aguirre', 'Micaela Giménez', 'Agustín Molina',
    ];

    private const COMMENTS = [
        5 => [
            'Excelente cancha, impecable.',
            'Muy buena atención y todo muy limpio.',
            'Reservar fue rapidísimo y el QR en la entrada anduvo perfecto.',
            'La iluminación de noche es muy buena.',
            'Volvimos con los chicos, todo 10.',
        ],
        4 => [
            'Muy linda cancha, los vestuarios podrían estar mejor.',
            'Buena experiencia, un poco de demora para entrar.',
            'Todo bien, el estacionamiento se llena rápido.',
            'Buen precio para lo que ofrece.',
        ],
        3 => [
            'Correcta, pero la red estaba algo gastada.',
            'Estuvo bien, aunque había mucha gente en el complejo.',
        ],
    ];

    public function run(): void
    {
        if (User::where('email', 'like', '%@' . self::EMAIL_DOMAIN)->exists()) {
            $this->command?->warn('Los datos de demostración ya están cargados. Para sacarlos: php artisan demo:borrar');

            return;
        }

        $courts = Court::with('sport')->where('is_active', true)->get();

        if ($courts->isEmpty()) {
            $this->command?->warn('No hay canchas activas: cargá las canchas antes de los datos de demostración.');

            return;
        }

        mt_srand(2026);

        [$clients, $bookings, $reviews] = DB::transaction(function () use ($courts) {
            $clients = $this->createClients();
            $bookings = $this->createBookings($courts, $clients);
            $reviews = $this->createReviews($bookings);

            return [$clients, $bookings, $reviews];
        });

        $this->command?->info(
            "Datos de demostración: {$clients->count()} clientes, {$bookings->count()} reservas pasadas, {$reviews} comentarios."
        );
    }

    private function createClients()
    {
        return collect(self::CLIENTS)->map(function (string $name) {
            $user = new User([
                'name' => $name,
                'email' => Str::slug($name, '.') . '@' . self::EMAIL_DOMAIN,
                'password' => bcrypt(Str::random(32)),
                'role' => 'usuario',
            ]);
            $user->email_verified_at = now();
            $user->created_at = now()->subDays(mt_rand(25, 60));
            $user->save();

            return $user;
        });
    }

    private function createBookings($courts, $clients)
    {
        $bookings = collect();

        for ($attempt = 0; $bookings->count() < self::BOOKINGS && $attempt < self::BOOKINGS * 5; $attempt++) {
            $court = $courts[mt_rand(0, $courts->count() - 1)];
            $client = $clients[mt_rand(0, $clients->count() - 1)];
            $date = today()->subDays(mt_rand(3, 22))->toDateString();
            [$start, $end] = $this->randomSlot($court);

            // Si el horario ya existe (ej. generado por turns:generate) solo se
            // usa si esta libre; si no, se prueba con otro.
            $turn = Turn::firstOrNew([
                'court_id' => $court->id,
                'date' => $date,
                'start_time' => $start,
            ]);

            if ($turn->exists && $turn->status !== 'available') {
                continue;
            }

            $bookedAt = Carbon::parse($date)->subDays(mt_rand(0, 4))->setTime(mt_rand(9, 22), mt_rand(0, 59));

            $turn->fill([
                'user_id' => $client->id,
                'end_time' => $end,
                'price' => $court->price_per_hour,
                'status' => 'booked',
                'qr_code' => (string) Str::uuid(),
            ]);
            $turn->saveQuietly();

            $payment = new Payment([
                'turn_id' => $turn->id,
                'user_id' => $client->id,
                'amount' => $court->price_per_hour,
                'method' => 'mp',
                'status' => 'approved',
            ]);
            $payment->created_at = $bookedAt;
            $payment->updated_at = $bookedAt;
            $payment->save();

            $bookings->push($turn);
        }

        return $bookings;
    }

    private function createReviews($bookings): int
    {
        $created = 0;

        $pairs = $bookings
            ->unique(fn (Turn $turn) => $turn->court_id . '-' . $turn->user_id)
            ->shuffle(2026)
            ->take(self::REVIEWS);

        foreach ($pairs as $turn) {
            $rating = $this->randomRating();
            $comments = self::COMMENTS[$rating];

            $review = new CourtReview([
                'court_id' => $turn->court_id,
                'user_id' => $turn->user_id,
                'rating' => $rating,
                'comment' => $comments[mt_rand(0, count($comments) - 1)],
            ]);
            $review->created_at = Carbon::parse($turn->date)->addDays(mt_rand(0, 2))->setTime(mt_rand(10, 23), mt_rand(0, 59));
            $review->updated_at = $review->created_at;
            $review->save();

            $created++;
        }

        return $created;
    }

    /**
     * Un horario valido segun la configuracion del deporte (igual que turns:generate).
     */
    private function randomSlot(Court $court): array
    {
        $sport = $court->sport;
        $start = Carbon::parse($sport->default_start_time ?? '08:00');
        $end = Carbon::parse($sport->default_end_time ?? '22:00');
        $duration = (int) ($sport->slot_duration_minutes ?? 60);

        $slots = [];
        for ($slot = $start->copy(); $slot->copy()->addMinutes($duration)->lessThanOrEqualTo($end); $slot->addMinutes($duration)) {
            $slots[] = [$slot->format('H:i:s'), $slot->copy()->addMinutes($duration)->format('H:i:s')];
        }

        return $slots[mt_rand(0, count($slots) - 1)];
    }

    /**
     * Mayormente 5 y 4, con algun 3 para que sea creible.
     */
    private function randomRating(): int
    {
        $roll = mt_rand(1, 100);

        return match (true) {
            $roll <= 55 => 5,
            $roll <= 90 => 4,
            default => 3,
        };
    }
}
