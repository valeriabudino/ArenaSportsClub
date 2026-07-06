<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnQrControllerTest extends TestCase
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
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 5000,
            'status' => 'available',
        ], $attributes));
    }

    public function test_owner_can_view_the_qr_of_a_booked_turn(): void
    {
        $user = User::factory()->create();
        $turn = $this->makeTurn(['user_id' => $user->id]);
        $turn->update(['status' => 'booked']);

        $response = $this->actingAs($user)->get(route('reservations.qr', $turn));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_another_user_cannot_view_someone_elses_qr(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $turn = $this->makeTurn(['user_id' => $owner->id]);
        $turn->update(['status' => 'booked']);

        $response = $this->actingAs($stranger)->get(route('reservations.qr', $turn));

        $response->assertForbidden();
    }

    public function test_qr_is_not_available_for_a_turn_that_is_not_booked(): void
    {
        $user = User::factory()->create();
        $turn = $this->makeTurn(['user_id' => $user->id, 'status' => 'pending_payment']);

        $response = $this->actingAs($user)->get(route('reservations.qr', $turn));

        $response->assertNotFound();
    }
}
