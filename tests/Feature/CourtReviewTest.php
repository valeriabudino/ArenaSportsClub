<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\CourtReview;
use App\Models\Sport;
use App\Models\Turn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CourtReviewTest extends TestCase
{
    use RefreshDatabase;

    private function makeCourt(): Court
    {
        return Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);
    }

    private function makeTurn(Court $court, User $user, array $attributes = []): Turn
    {
        return Turn::create(array_merge([
            'court_id' => $court->id,
            'user_id' => $user->id,
            'date' => now()->subDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'price' => 5000,
            'status' => 'booked',
        ], $attributes));
    }

    public function test_a_user_without_any_turn_cannot_review(): void
    {
        $court = $this->makeCourt();
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('public.court-detail', ['court' => $court])
            ->set('rating', 5)
            ->set('comment', 'Buenisima')
            ->call('saveReview');

        $this->assertDatabaseMissing('court_reviews', ['court_id' => $court->id, 'user_id' => $user->id]);
    }

    public function test_a_user_with_a_future_booked_turn_cannot_review_yet(): void
    {
        $court = $this->makeCourt();
        $user = User::factory()->create();

        $this->makeTurn($court, $user, [
            'date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);

        $this->actingAs($user);

        Volt::test('public.court-detail', ['court' => $court])
            ->set('rating', 5)
            ->call('saveReview');

        $this->assertDatabaseMissing('court_reviews', ['court_id' => $court->id, 'user_id' => $user->id]);
    }

    public function test_a_user_with_a_pending_payment_turn_cannot_review(): void
    {
        $court = $this->makeCourt();
        $user = User::factory()->create();

        $this->makeTurn($court, $user, ['status' => 'pending_payment']);

        $this->actingAs($user);

        Volt::test('public.court-detail', ['court' => $court])
            ->set('rating', 5)
            ->call('saveReview');

        $this->assertDatabaseMissing('court_reviews', ['court_id' => $court->id, 'user_id' => $user->id]);
    }

    public function test_a_user_with_a_completed_turn_can_review(): void
    {
        $court = $this->makeCourt();
        $user = User::factory()->create();

        $this->makeTurn($court, $user);

        $this->actingAs($user);

        Volt::test('public.court-detail', ['court' => $court])
            ->set('rating', 4)
            ->set('comment', 'Muy buena')
            ->call('saveReview');

        $this->assertDatabaseHas('court_reviews', [
            'court_id' => $court->id,
            'user_id' => $user->id,
            'rating' => 4,
        ]);
    }

    public function test_a_user_cannot_review_the_same_court_twice(): void
    {
        $court = $this->makeCourt();
        $user = User::factory()->create();

        $this->makeTurn($court, $user);

        CourtReview::create([
            'court_id' => $court->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Primera valoracion',
        ]);

        $this->actingAs($user);

        Volt::test('public.court-detail', ['court' => $court])
            ->set('rating', 1)
            ->set('comment', 'Segunda valoracion')
            ->call('saveReview');

        $this->assertSame(1, CourtReview::where('court_id', $court->id)->where('user_id', $user->id)->count());
        $this->assertDatabaseHas('court_reviews', ['court_id' => $court->id, 'user_id' => $user->id, 'rating' => 5]);
    }
}
