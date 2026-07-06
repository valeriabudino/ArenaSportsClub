<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\Turn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnObserverTest extends TestCase
{
    use RefreshDatabase;

    private function makeTurn(string $status = 'available'): Turn
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        return Turn::create([
            'court_id' => $court->id,
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 5000,
            'status' => $status,
        ]);
    }

    public function test_booking_a_turn_generates_a_qr_code(): void
    {
        $turn = $this->makeTurn();

        $turn->update(['status' => 'booked']);

        $this->assertNotNull($turn->fresh()->qr_code);
    }

    public function test_a_second_update_to_booked_does_not_change_the_qr_code(): void
    {
        $turn = $this->makeTurn();

        $turn->update(['status' => 'booked']);
        $firstQrCode = $turn->fresh()->qr_code;

        $turn->update(['status' => 'booked']);

        $this->assertSame($firstQrCode, $turn->fresh()->qr_code);
    }
}
