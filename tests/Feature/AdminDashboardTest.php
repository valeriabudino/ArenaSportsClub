<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Payment;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private Court $court;

    private User $user;

    private int $hour = 8;

    protected function setUp(): void
    {
        parent::setUp();

        $this->court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 1000,
        ]);

        $this->user = User::factory()->create();
    }

    private function turn(string $date, string $status): Turn
    {
        // Cada turno en un horario distinto, por el indice unico cancha+fecha+hora.
        $start = sprintf('%02d:00', $this->hour++);

        return Turn::withoutEvents(fn () => Turn::create([
            'court_id' => $this->court->id,
            'user_id' => $status === 'available' ? null : $this->user->id,
            'date' => $date,
            'start_time' => $start,
            'end_time' => $start,
            'price' => 1000,
            'status' => $status,
        ]));
    }

    private function payment(string $status, int $amount, $createdAt = null): void
    {
        $payment = Payment::create([
            'turn_id' => $this->turn(today()->toDateString(), 'available')->id,
            'user_id' => $this->user->id,
            'amount' => $amount,
            'status' => $status,
        ]);

        if ($createdAt) {
            $payment->forceFill(['created_at' => $createdAt])->save();
        }
    }

    public function test_dashboard_shows_the_real_metrics(): void
    {
        $today = today()->toDateString();

        // Reservas: 2 hoy + 1 futura cuentan como proximas; la de ayer no.
        $this->turn($today, 'booked');
        $this->turn($today, 'booked');
        $this->turn(today()->addDays(3)->toDateString(), 'booked');
        $this->turn(today()->subDay()->toDateString(), 'booked');

        // Pagos pendientes: solo desde hoy.
        $this->turn(today()->addDay()->toDateString(), 'pending_payment');
        $this->turn(today()->subMonth()->toDateString(), 'pending_payment');

        // Ingresos del mes: approved + cancelled_no_refund de este mes.
        $this->payment('approved', 25000);
        $this->payment('cancelled_no_refund', 5000);
        $this->payment('approved', 99000, now()->subMonthNoOverflow()); // mes anterior
        $this->payment('rejected', 7000);
        $this->payment('refund_pending', 3000);
        $this->payment('refunded', 4000);

        // Canchas activas: la de setUp + una activa; la inactiva y la eliminada no.
        Court::create(['sport_id' => $this->court->sport_id, 'name' => 'Activa', 'price_per_hour' => 1]);
        Court::create(['sport_id' => $this->court->sport_id, 'name' => 'Inactiva', 'price_per_hour' => 1, 'is_active' => false]);
        Court::create(['sport_id' => $this->court->sport_id, 'name' => 'Borrada', 'price_per_hour' => 1])->delete();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('todayBookings', 2)
            ->assertViewHas('upcomingBookings', 3)
            ->assertViewHas('pendingPayments', 1)
            ->assertViewHas('monthIncome', 30000)
            ->assertViewHas('activeCourts', 2)
            ->assertViewHas('pendingRefunds', 1)
            ->assertSee('$30.000');
    }

    public function test_a_regular_user_cannot_see_the_dashboard(): void
    {
        $this->actingAs($this->user)->get(route('admin.dashboard'))->assertForbidden();
    }
}
