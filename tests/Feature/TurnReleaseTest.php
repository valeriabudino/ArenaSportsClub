<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\Support\FakeWhatsAppSender;
use Tests\TestCase;

/**
 * Cuando un turno vuelve a quedar disponible (cancelacion, pago rechazado,
 * vencimiento) no puede conservar el QR ni la marca de recordatorio del
 * usuario anterior.
 */
class TurnReleaseTest extends TestCase
{
    use RefreshDatabase;

    private FakeWhatsAppSender $whatsApp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->whatsApp = new FakeWhatsAppSender();
        $this->app->instance(WhatsAppSenderInterface::class, $this->whatsApp);
    }

    private function makeTurn(): Turn
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        return Turn::create([
            'court_id' => $court->id,
            'date' => today()->addDays(3)->toDateString(),
            'start_time' => '20:00',
            'end_time' => '21:00',
            'price' => 5000,
            'status' => 'available',
        ]);
    }

    public function test_releasing_a_turn_clears_its_qr_and_reminder_mark(): void
    {
        $turn = $this->makeTurn();
        $turn->update(['user_id' => User::factory()->create()->id, 'status' => 'booked']);
        $turn->update(['reminder_sent_at' => now()]);

        $this->assertNotNull($turn->fresh()->qr_code);

        $turn->update(['status' => 'available', 'user_id' => null]);

        $this->assertNull($turn->fresh()->qr_code);
        $this->assertNull($turn->fresh()->reminder_sent_at);
    }

    public function test_a_turn_rebooked_after_a_cancellation_gets_a_new_qr_and_confirmation(): void
    {
        $turn = $this->makeTurn();
        $juan = User::factory()->create(['phone' => '5493718000001']);
        $ana = User::factory()->create(['phone' => '5493718000002']);

        // Juan reserva y paga (el webhook lo pasa a booked).
        $turn->update(['user_id' => $juan->id, 'status' => 'booked']);
        $juanQr = $turn->fresh()->qr_code;

        // Juan cancela desde "Mis reservas".
        $this->actingAs($juan);
        Volt::test('pages.reservations.index')->call('cancel', $turn->id);
        $this->assertSame('available', $turn->fresh()->status);

        // Ana reserva y paga el mismo turno.
        $turn->fresh()->update(['user_id' => $ana->id, 'status' => 'booked']);
        $anaQr = $turn->fresh()->qr_code;

        $this->assertNotNull($anaQr);
        $this->assertNotSame($juanQr, $anaQr);

        // A Ana tambien le llega la confirmacion por WhatsApp.
        $this->assertSame(
            ['5493718000001', '5493718000002'],
            array_column($this->whatsApp->sent, 'phone'),
        );

        // En el horario del turno, el QR de Juan ya no entra y el de Ana si.
        $this->travelTo(now()->setDateFrom($turn->date)->setTime(20, 5));

        $this->postJson(route('api.reservations.validate'), ['qr_code' => $juanQr])
            ->assertJson(['valido' => false]);

        $this->postJson(route('api.reservations.validate'), ['qr_code' => $anaQr])
            ->assertJson(['valido' => true, 'cliente' => $ana->name]);
    }
}
