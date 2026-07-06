<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReservationValidationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeTurn(array $attributes = []): Turn
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        return Turn::create(array_merge([
            'court_id' => $court->id,
            'user_id' => User::factory()->create(['name' => 'Juan Perez'])->id,
            'date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'end_time' => now()->addHour()->format('H:i:s'),
            'price' => 5000,
            'status' => 'booked',
            'qr_code' => (string) Str::uuid(),
        ], $attributes));
    }

    public function test_approves_a_paid_reservation_within_the_time_window(): void
    {
        $turn = $this->makeTurn();

        $response = $this->postJson('/api/reservations/validate', [
            'qr_code' => $turn->qr_code,
            'terminal_id' => 'PUERTA_PRINCIPAL',
        ]);

        $response->assertOk()->assertJson([
            'valido' => true,
            'mensaje' => 'Reserva confirmada. Bienvenido.',
            'cliente' => 'Juan Perez',
        ]);
    }

    public function test_does_not_include_cliente_when_rejected(): void
    {
        $response = $this->postJson('/api/reservations/validate', ['qr_code' => 'no-existe']);

        $response->assertExactJson([
            'valido' => false,
            'mensaje' => 'Reserva impaga o fuera de horario permitido.',
        ]);
    }

    public function test_the_same_qr_code_can_be_scanned_more_than_once(): void
    {
        $turn = $this->makeTurn();

        $this->postJson('/api/reservations/validate', ['qr_code' => $turn->qr_code])
            ->assertJson(['valido' => true]);

        $this->postJson('/api/reservations/validate', ['qr_code' => $turn->qr_code])
            ->assertJson(['valido' => true]);
    }

    public function test_rejects_an_unknown_qr_code(): void
    {
        $response = $this->postJson('/api/reservations/validate', ['qr_code' => 'no-existe']);

        $response->assertJson([
            'valido' => false,
            'mensaje' => 'Reserva impaga o fuera de horario permitido.',
        ]);
    }

    public function test_rejects_a_reservation_that_is_not_fully_paid(): void
    {
        $turn = $this->makeTurn(['status' => 'pending_payment']);

        $response = $this->postJson('/api/reservations/validate', ['qr_code' => $turn->qr_code]);

        $response->assertJson(['valido' => false]);
    }

    public function test_rejects_a_scan_more_than_15_minutes_before_the_start(): void
    {
        $turn = $this->makeTurn([
            'date' => now()->addMinutes(30)->toDateString(),
            'start_time' => now()->addMinutes(30)->format('H:i:s'),
            'end_time' => now()->addMinutes(90)->format('H:i:s'),
        ]);

        $response = $this->postJson('/api/reservations/validate', ['qr_code' => $turn->qr_code]);

        $response->assertJson(['valido' => false]);
    }

    public function test_approves_a_scan_up_until_the_exact_end_time(): void
    {
        $turn = $this->makeTurn([
            'date' => now()->toDateString(),
            'start_time' => now()->subHour()->format('H:i:s'),
            'end_time' => now()->addMinute()->format('H:i:s'),
        ]);

        $response = $this->postJson('/api/reservations/validate', ['qr_code' => $turn->qr_code]);

        $response->assertJson(['valido' => true]);
    }

    public function test_rejects_a_scan_after_the_turn_has_finished(): void
    {
        $turn = $this->makeTurn([
            'date' => now()->toDateString(),
            'start_time' => now()->subHours(2)->format('H:i:s'),
            'end_time' => now()->subMinute()->format('H:i:s'),
        ]);

        $response = $this->postJson('/api/reservations/validate', ['qr_code' => $turn->qr_code]);

        $response->assertJson(['valido' => false]);
    }
}
