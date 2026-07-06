<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\CourtReview;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdminReviewsTest extends TestCase
{
    use RefreshDatabase;

    private function makeReview(array $attributes = []): CourtReview
    {
        $court = Court::create([
            'sport_id' => Sport::create(['name' => 'Fútbol'])->id,
            'name' => 'Cancha 1',
            'price_per_hour' => 5000,
        ]);

        return CourtReview::create(array_merge([
            'court_id' => $court->id,
            'user_id' => User::factory()->create()->id,
            'rating' => 5,
            'comment' => 'Muy buena cancha',
        ], $attributes));
    }

    public function test_admin_can_view_the_reviews_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeReview(['comment' => 'Excelente atención']);

        $response = $this->actingAs($admin)->get(route('admin.reviews.index'));

        $response->assertOk();
        $response->assertSee('Excelente atención');
    }

    public function test_a_regular_user_cannot_view_the_reviews_list(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->get(route('admin.reviews.index'))->assertForbidden();
    }

    public function test_admin_can_delete_a_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $review = $this->makeReview();

        $this->actingAs($admin);

        Volt::test('admin.reviews.index')
            ->call('delete', $review->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('court_reviews', ['id' => $review->id]);
    }
}
