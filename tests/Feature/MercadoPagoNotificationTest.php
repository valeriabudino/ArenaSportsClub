<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use App\Services\ReservationPaymentHandler;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use App\Support\MercadoPagoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Support\FakeWhatsAppSender;
use Tests\TestCase;

class MercadoPagoNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function webhookRequest(array $query, array $body = []): Request
    {
        $request = Request::create('/api/webhooks/mercadopago?' . http_build_query($query), 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($body));

        return $request;
    }

    public function test_ipn_format_uses_the_id_as_the_payment_id(): void
    {
        $request = $this->webhookRequest(['topic' => 'payment', 'id' => '181788747964']);

        $this->assertTrue(MercadoPagoNotification::isPayment($request));
        $this->assertSame('181788747964', MercadoPagoNotification::paymentId($request));
    }

    public function test_webhooks_format_uses_data_id_and_not_the_notification_id(): void
    {
        // Asi llega el formato nuevo: "id" es el numero del aviso.
        $request = $this->webhookRequest(
            ['data.id' => '181788747964', 'type' => 'payment'],
            ['id' => 126481234, 'type' => 'payment', 'action' => 'payment.created', 'data' => ['id' => '181788747964']],
        );

        $this->assertTrue(MercadoPagoNotification::isPayment($request));
        $this->assertSame('181788747964', MercadoPagoNotification::paymentId($request));
    }

    public function test_webhooks_format_with_data_id_only_in_the_url(): void
    {
        $request = $this->webhookRequest(['data.id' => '181788747964', 'type' => 'payment']);

        $this->assertSame('181788747964', MercadoPagoNotification::paymentId($request));
    }

    public function test_other_notification_types_are_ignored(): void
    {
        $request = $this->webhookRequest(['topic' => 'merchant_order', 'id' => '999']);

        $this->assertFalse(MercadoPagoNotification::isPayment($request));
        $this->postJson('/api/webhooks/mercadopago?topic=merchant_order&id=999')->assertJson(['ignored' => true]);
    }

    public function test_a_notification_without_payment_id_is_ignored(): void
    {
        // Formato nuevo sin data.id: el "id" suelto es del aviso, no sirve.
        $this->postJson('/api/webhooks/mercadopago', ['id' => 126481234, 'type' => 'payment'])
            ->assertJson(['ignored' => true]);
    }

    public function test_when_a_payment_is_approved_the_other_pending_payments_of_the_turn_expire(): void
    {
        $this->app->instance(WhatsAppSenderInterface::class, new FakeWhatsAppSender());

        $user = User::factory()->create();
        $court = Court::create(['sport_id' => Sport::create(['name' => 'Fútbol'])->id, 'name' => 'C', 'price_per_hour' => 1000]);
        $turn = Turn::create([
            'court_id' => $court->id, 'user_id' => $user->id, 'date' => today()->addDay()->toDateString(),
            'start_time' => '20:00', 'end_time' => '21:00', 'price' => 1000,
            'status' => 'pending_payment', 'reserved_until' => now()->addMinutes(10),
        ]);

        $make = fn () => Payment::create(['turn_id' => $turn->id, 'user_id' => $user->id, 'amount' => 1000, 'method' => 'mp', 'status' => 'pending']);
        [$first, $second, $paid] = [$make(), $make(), $make()];

        app(ReservationPaymentHandler::class)->apply($paid, 'approved', 123);

        $this->assertSame('booked', $turn->fresh()->status);
        $this->assertSame('approved', $paid->fresh()->status);
        $this->assertSame('expired', $first->fresh()->status);
        $this->assertSame('expired', $second->fresh()->status);
    }
}
