<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendTurnRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function makeTurn(array $attributes = []): Turn
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        $user = User::factory()->create(['phone' => '5493718000000']);

        return Turn::create(array_merge([
            'court_id' => $court->id,
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'end_time' => now()->addHour()->format('H:i:s'),
            'price' => 5000,
            'status' => 'booked',
            'qr_code' => (string) \Illuminate\Support\Str::uuid(),
        ], $attributes));
    }

    public function test_sends_reminder_for_a_turn_starting_within_the_11_to_12_hour_window(): void
    {
        $fake = new FakeWhatsAppSender();
        $this->app->instance(WhatsAppSenderInterface::class, $fake);

        $turn = $this->makeTurn([
            'date' => now()->addHours(11.5)->toDateString(),
            'start_time' => now()->addHours(11.5)->format('H:i:s'),
        ]);

        $this->artisan('turnos:recordatorio')->assertSuccessful();

        $this->assertCount(1, $fake->sent);
        $this->assertSame($turn->user->phone, $fake->sent[0]['phone']);
        $this->assertNotNull($turn->fresh()->reminder_sent_at);
    }

    public function test_does_not_send_a_second_reminder_if_already_notified(): void
    {
        $fake = new FakeWhatsAppSender();
        $this->app->instance(WhatsAppSenderInterface::class, $fake);

        $this->makeTurn([
            'date' => now()->addHours(11.5)->toDateString(),
            'start_time' => now()->addHours(11.5)->format('H:i:s'),
            'reminder_sent_at' => now()->subHour(),
        ]);

        $this->artisan('turnos:recordatorio')->assertSuccessful();

        $this->assertCount(0, $fake->sent);
    }

    public function test_does_not_send_a_reminder_for_a_turn_outside_the_window(): void
    {
        $fake = new FakeWhatsAppSender();
        $this->app->instance(WhatsAppSenderInterface::class, $fake);

        $this->makeTurn([
            'date' => now()->addHours(2)->toDateString(),
            'start_time' => now()->addHours(2)->format('H:i:s'),
        ]);

        $this->artisan('turnos:recordatorio')->assertSuccessful();

        $this->assertCount(0, $fake->sent);
    }

    public function test_does_not_send_a_reminder_for_a_cancelled_turn(): void
    {
        $fake = new FakeWhatsAppSender();
        $this->app->instance(WhatsAppSenderInterface::class, $fake);

        $this->makeTurn([
            'date' => now()->addHours(11.5)->toDateString(),
            'start_time' => now()->addHours(11.5)->format('H:i:s'),
            'status' => 'cancelled',
        ]);

        $this->artisan('turnos:recordatorio')->assertSuccessful();

        $this->assertCount(0, $fake->sent);
    }

    public function test_does_not_mark_the_reminder_as_sent_when_the_provider_fails(): void
    {
        $fake = new FakeWhatsAppSender(succeeds: false);
        $this->app->instance(WhatsAppSenderInterface::class, $fake);

        $turn = $this->makeTurn([
            'date' => now()->addHours(11.5)->toDateString(),
            'start_time' => now()->addHours(11.5)->format('H:i:s'),
        ]);

        $this->artisan('turnos:recordatorio')->assertSuccessful();

        $this->assertCount(1, $fake->sent);
        $this->assertNull($turn->fresh()->reminder_sent_at);
    }
}

class FakeWhatsAppSender implements WhatsAppSenderInterface
{
    public array $sent = [];

    public function __construct(private readonly bool $succeeds = true) {}

    public function sendMessage(string $phoneNumber, string $message, ?string $mediaUrl = null): bool
    {
        $this->sent[] = ['phone' => $phoneNumber, 'message' => $message, 'mediaUrl' => $mediaUrl];

        return $this->succeeds;
    }
}
