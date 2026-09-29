<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use App\Services\MercadoPagoService;
use App\Services\ReservationPaymentHandler;
use App\Services\WhatsApp\WhatsAppSenderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\Support\FakeWhatsAppSender;
use Tests\TestCase;

/**
 * Un turno en pending_payment queda reservado Turn::PAYMENT_WINDOW_MINUTES
 * para quien lo inicio; despues se libera y lo puede tomar otro usuario.
 */
class ReservationExpiryTest extends TestCase
{
    use RefreshDatabase;

    private Court $court;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(WhatsAppSenderInterface::class, new FakeWhatsAppSender());

        $this->court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);
    }

    private function turn(array $attributes = []): Turn
    {
        return Turn::create(array_merge([
            'court_id' => $this->court->id,
            'date' => today()->addDays(2)->toDateString(),
            'start_time' => '20:00',
            'end_time' => '21:00',
            'price' => 5000,
            'status' => 'available',
        ], $attributes));
    }

    private function heldBy(User $user, $reservedUntil): Turn
    {
        return $this->turn([
            'user_id' => $user->id,
            'status' => 'pending_payment',
            'reserved_until' => $reservedUntil,
        ]);
    }

    private function payment(Turn $turn, User $user, string $status = 'pending'): Payment
    {
        return Payment::create([
            'turn_id' => $turn->id,
            'user_id' => $user->id,
            'amount' => 5000,
            'method' => 'mp',
            'status' => $status,
        ]);
    }

    private function fakeMercadoPago(): void
    {
        $this->mock(MercadoPagoService::class, fn ($mock) => $mock
            ->shouldReceive('createPreference')
            ->andReturn(['payment' => null, 'checkout_url' => 'https://mp.test/checkout']));
    }

    private function reserveAs(User $user, Turn $turn)
    {
        $this->actingAs($user);

        return Volt::test('public.court-detail', ['court' => $this->court])->call('reserve', $turn->id);
    }

    // --- Reserva y vencimiento ---

    public function test_reserving_holds_the_turn_for_the_payment_window(): void
    {
        $this->freezeTime();
        $this->fakeMercadoPago();
        $user = User::factory()->create();
        $turn = $this->turn();

        $this->reserveAs($user, $turn)->assertRedirect('https://mp.test/checkout');

        $turn->refresh();
        $this->assertSame('pending_payment', $turn->status);
        $this->assertSame($user->id, $turn->user_id);
        // La base guarda segundos (sin microsegundos), por eso se compara formateado.
        $this->assertSame(
            now()->addMinutes(Turn::PAYMENT_WINDOW_MINUTES)->format('Y-m-d H:i:s'),
            $turn->reserved_until->format('Y-m-d H:i:s'),
        );
    }

    public function test_a_turn_inside_its_payment_window_cannot_be_taken(): void
    {
        $this->fakeMercadoPago();
        $juan = User::factory()->create();
        $ana = User::factory()->create();
        $turn = $this->heldBy($juan, now()->addMinutes(5));

        $this->reserveAs($ana, $turn)->assertNoRedirect();

        $this->assertSame($juan->id, $turn->fresh()->user_id);
    }

    public function test_an_expired_reservation_is_released_and_someone_else_can_book_it(): void
    {
        $this->fakeMercadoPago();
        $juan = User::factory()->create();
        $ana = User::factory()->create();
        $turn = $this->heldBy($juan, now()->subMinute());
        $juanPayment = $this->payment($turn, $juan);

        $this->reserveAs($ana, $turn)->assertRedirect('https://mp.test/checkout');

        $this->assertSame($ana->id, $turn->fresh()->user_id);
        $this->assertSame('expired', $juanPayment->fresh()->status);
    }

    public function test_pending_turns_without_a_deadline_are_treated_as_expired(): void
    {
        // Los creados antes de que existiera el vencimiento.
        $turn = $this->turn(['user_id' => User::factory()->create()->id, 'status' => 'pending_payment']);

        $this->assertSame(1, Turn::releaseExpiredReservations());
        $this->assertSame('available', $turn->fresh()->status);
        $this->assertNull($turn->fresh()->user_id);
    }

    public function test_paying_an_expired_reservation_shows_a_message(): void
    {
        $juan = User::factory()->create();
        $turn = $this->heldBy($juan, now()->subMinute());

        $this->actingAs($juan);
        Volt::test('pages.reservations.index')
            ->call('pay', $turn->id)
            ->assertSee('Se venció el tiempo para pagar');

        $this->assertSame('available', $turn->fresh()->status);
    }

    public function test_the_command_releases_expired_reservations(): void
    {
        $this->heldBy(User::factory()->create(), now()->subMinute());
        $this->turn([
            'start_time' => '21:00',
            'end_time' => '22:00',
            'user_id' => User::factory()->create()->id,
            'status' => 'pending_payment',
            'reserved_until' => now()->addMinutes(5),
        ]);

        $this->artisan('turnos:liberar-vencidos')->expectsOutput('Turnos liberados: 1')->assertSuccessful();
    }

    // --- Preferencia de Mercado Pago ---

    public function test_the_mercado_pago_preference_expires_with_the_reservation(): void
    {
        $user = User::factory()->create();
        $turn = $this->heldBy($user, now()->addMinutes(15));

        $data = app(MercadoPagoService::class)->preferenceData($turn, $this->payment($turn, $user));

        $this->assertTrue($data['expires']);
        $this->assertSame($turn->reserved_until->format('Y-m-d\TH:i:s.vP'), $data['expiration_date_to']);
    }

    // --- Webhook (ReservationPaymentHandler) ---

    public function test_an_approved_payment_books_the_turn_for_its_holder(): void
    {
        $juan = User::factory()->create();
        $turn = $this->heldBy($juan, now()->addMinutes(5));
        $payment = $this->payment($turn, $juan);

        app(ReservationPaymentHandler::class)->apply($payment, 'approved', 123);

        $this->assertSame('booked', $turn->fresh()->status);
        $this->assertNotNull($turn->fresh()->qr_code);
        $this->assertSame('approved', $payment->fresh()->status);
        $this->assertSame('123', (string) $payment->fresh()->mp_payment_id);
    }

    public function test_a_late_approved_payment_does_not_steal_someone_elses_turn(): void
    {
        $juan = User::factory()->create();
        $ana = User::factory()->create();
        $turn = $this->heldBy($juan, now()->subMinute());
        $juanPayment = $this->payment($turn, $juan);

        // El turno se libero y Ana ya lo pago.
        Turn::releaseExpiredReservations();
        $turn->fresh()->update(['user_id' => $ana->id, 'status' => 'booked']);
        $anaQr = $turn->fresh()->qr_code;

        app(ReservationPaymentHandler::class)->apply($juanPayment->fresh(), 'approved');

        $this->assertSame($ana->id, $turn->fresh()->user_id);
        $this->assertSame($anaQr, $turn->fresh()->qr_code);
        $this->assertSame('refund_pending', $juanPayment->fresh()->status);
    }

    public function test_a_late_approved_payment_books_the_turn_if_nobody_took_it(): void
    {
        $juan = User::factory()->create();
        $turn = $this->heldBy($juan, now()->subMinute());
        $payment = $this->payment($turn, $juan);
        Turn::releaseExpiredReservations();

        app(ReservationPaymentHandler::class)->apply($payment->fresh(), 'approved');

        $this->assertSame('booked', $turn->fresh()->status);
        $this->assertSame($juan->id, $turn->fresh()->user_id);
        $this->assertSame('approved', $payment->fresh()->status);
    }

    public function test_a_rejected_payment_does_not_release_someone_elses_turn(): void
    {
        $juan = User::factory()->create();
        $ana = User::factory()->create();
        $turn = $this->heldBy($ana, now()->addMinutes(10));
        $juanPayment = $this->payment($turn, $juan);

        app(ReservationPaymentHandler::class)->apply($juanPayment, 'rejected');

        $this->assertSame('pending_payment', $turn->fresh()->status);
        $this->assertSame($ana->id, $turn->fresh()->user_id);
    }

    public function test_a_rejected_payment_releases_the_holders_turn(): void
    {
        $juan = User::factory()->create();
        $turn = $this->heldBy($juan, now()->addMinutes(10));

        app(ReservationPaymentHandler::class)->apply($this->payment($turn, $juan), 'rejected');

        $this->assertSame('available', $turn->fresh()->status);
        $this->assertNull($turn->fresh()->reserved_until);
    }

    public function test_repeated_notifications_are_ignored_after_a_cancellation(): void
    {
        $juan = User::factory()->create();
        $turn = $this->turn();
        $payment = $this->payment($turn, $juan, 'cancelled_no_refund');

        app(ReservationPaymentHandler::class)->apply($payment, 'approved');

        $this->assertSame('available', $turn->fresh()->status);
        $this->assertSame('cancelled_no_refund', $payment->fresh()->status);
    }
}
