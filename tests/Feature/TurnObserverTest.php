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

    private function makeTurn(string $status = 'available', ?string $phone = '5493718000000'): Turn
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        $user = User::factory()->create(['phone' => $phone]);

        return Turn::create([
            'court_id' => $court->id,
            'user_id' => $user->id,
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
}
