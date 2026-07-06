<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakeWhatsAppSender;
use Tests\TestCase;

class TurnObserverTest extends TestCase
{
    use RefreshDatabase;

    private FakeWhatsAppSender $whatsApp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->whatsApp = new FakeWhatsAppSender();
        $this->app->instance(WhatsAppSenderInterface::class, $this->whatsApp);
    }

    private function makeTurn(string $status = 'available', ?string $phone = '5493718000000', array $attributes = []): Turn
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        $user = User::factory()->create(['phone' => $phone]);

        return Turn::create(array_merge([
            'court_id' => $court->id,
            'user_id' => $user->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 5000,
            'status' => $status,
        ], $attributes));
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

    public function test_booking_a_turn_sends_a_whatsapp_confirmation(): void
    {
        $turn = $this->makeTurn();

        $turn->update(['status' => 'booked']);

        $this->assertCount(1, $this->whatsApp->sent);
        $this->assertSame($turn->user->phone, $this->whatsApp->sent[0]['phone']);
    }

    public function test_a_second_update_to_booked_does_not_send_a_second_confirmation(): void
    {
        $turn = $this->makeTurn();

        $turn->update(['status' => 'booked']);
        $turn->update(['status' => 'booked']);

        $this->assertCount(1, $this->whatsApp->sent);
    }

    public function test_does_not_send_a_confirmation_when_the_user_has_no_phone(): void
    {
        $turn = $this->makeTurn(phone: null);

        $turn->update(['status' => 'booked']);

        $this->assertCount(0, $this->whatsApp->sent);
    }

    public function test_sends_the_qr_right_away_when_the_turn_starts_within_12_hours(): void
    {
        $turn = $this->makeTurn(attributes: [
            'date' => now()->addHours(2)->toDateString(),
            'start_time' => now()->addHours(2)->format('H:i:s'),
            'end_time' => now()->addHours(3)->format('H:i:s'),
        ]);

        $turn->update(['status' => 'booked']);

        $this->assertCount(1, $this->whatsApp->sent);
        $this->assertNotNull($this->whatsApp->sent[0]['mediaUrl']);
        $this->assertNotNull($turn->fresh()->reminder_sent_at);
    }

    public function test_sends_the_qr_right_away_when_the_turn_already_started(): void
    {
        // Reproduce el caso real: el pago se confirma un par de minutos despues
        // de la hora de inicio nominal del turno (usuario tardo en pagar).
        $turn = $this->makeTurn(attributes: [
            'date' => now()->toDateString(),
            'start_time' => now()->subMinutes(5)->format('H:i:s'),
            'end_time' => now()->addHour()->format('H:i:s'),
        ]);

        $turn->update(['status' => 'booked']);

        $this->assertCount(1, $this->whatsApp->sent);
        $this->assertNotNull($this->whatsApp->sent[0]['mediaUrl']);
        $this->assertNotNull($turn->fresh()->reminder_sent_at);
    }

    public function test_does_not_send_the_qr_right_away_when_the_turn_is_more_than_12_hours_away(): void
    {
        $turn = $this->makeTurn();

        $turn->update(['status' => 'booked']);

        $this->assertCount(1, $this->whatsApp->sent);
        $this->assertNull($this->whatsApp->sent[0]['mediaUrl']);
        $this->assertNull($turn->fresh()->reminder_sent_at);
    }
}
