<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\CourtReview;
use App\Models\Payment;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $futbol = Sport::create(['name' => 'Fútbol', 'default_start_time' => '08:00', 'default_end_time' => '22:00', 'slot_duration_minutes' => 60]);
        $padel = Sport::create(['name' => 'Pádel', 'default_start_time' => '07:00', 'default_end_time' => '23:00', 'slot_duration_minutes' => 90]);

        foreach ([[$futbol, 'Fútbol 1'], [$futbol, 'Fútbol 2'], [$padel, 'Pádel 1']] as [$sport, $name]) {
            Court::create(['sport_id' => $sport->id, 'name' => $name, 'price_per_hour' => 20000]);
        }
    }

    private function demoUsers()
    {
        return User::where('email', 'like', '%@' . DemoDataSeeder::EMAIL_DOMAIN);
    }

    public function test_the_seeder_creates_consistent_demo_data(): void
    {
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(12, $this->demoUsers()->count());
        $this->assertSame(0, $this->demoUsers()->whereNotNull('phone')->count());

        $bookings = Turn::where('status', 'booked')->get();
        $this->assertCount(40, $bookings);

        foreach ($bookings as $turn) {
            // Del pasado, con QR y con su pago aprobado por el precio de la cancha.
            $this->assertTrue($turn->date < today()->toDateString());
            $this->assertNotNull($turn->qr_code);
            $this->assertSame(1, $turn->payments()->where('status', 'approved')->where('amount', $turn->price)->count());
        }

        $reviews = CourtReview::all();
        $this->assertCount(15, $reviews);

        foreach ($reviews as $review) {
            // Misma regla que la app: solo comenta quien jugo en esa cancha.
            $this->assertTrue(
                $bookings->contains(fn ($t) => $t->court_id === $review->court_id && $t->user_id === $review->user_id)
            );
            $this->assertContains($review->rating, [3, 4, 5]);
        }
    }

    public function test_running_the_seeder_twice_does_not_duplicate_data(): void
    {
        $this->seed(DemoDataSeeder::class);
        $this->seed(DemoDataSeeder::class);

        $this->assertSame(12, $this->demoUsers()->count());
        $this->assertSame(40, Turn::where('status', 'booked')->count());
    }

    public function test_the_command_deletes_only_demo_data(): void
    {
        $realUser = User::factory()->create();
        $court = Court::first();
        $realTurn = Turn::create([
            'court_id' => $court->id, 'user_id' => $realUser->id, 'date' => today()->addDay()->toDateString(),
            'start_time' => '10:00', 'end_time' => '11:00', 'price' => 20000, 'status' => 'available',
        ]);

        $this->seed(DemoDataSeeder::class);

        $this->artisan('demo:borrar', ['--force' => true])->assertSuccessful();

        $this->assertSame(0, $this->demoUsers()->count());
        $this->assertSame(0, Turn::where('status', 'booked')->count());
        $this->assertSame(0, Payment::count());
        $this->assertSame(0, CourtReview::count());
        $this->assertTrue($realUser->fresh()->exists);
        $this->assertTrue($realTurn->fresh()->exists);
        $this->assertSame(3, Court::count());
    }

    public function test_home_stats_show_real_data(): void
    {
        User::factory()->create(['role' => 'admin']);

        $this->get('/')->assertOk()->assertSee('Clientes registrados')->assertSee('Nuevo');

        $this->seed(DemoDataSeeder::class);
        $average = number_format(CourtReview::avg('rating'), 1, ',', '.');

        $this->get('/')->assertOk()
            ->assertSeeInOrder(['12', 'Clientes registrados'])
            ->assertSeeInOrder(['3', 'Canchas disponibles'])
            ->assertSeeInOrder(['40', 'Reservas realizadas'])
            ->assertSeeInOrder([$average, 'Calificación promedio'])
            ->assertDontSee('+4500');
    }
}
