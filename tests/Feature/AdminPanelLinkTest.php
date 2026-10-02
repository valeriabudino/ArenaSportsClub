<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Desde la tienda el administrador tiene un acceso para volver al panel.
 */
class AdminPanelLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_sees_the_admin_panel_link_in_the_store(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get('/')->assertOk()
            ->assertSee('Panel de administración')
            ->assertSee(route('admin.dashboard'));
    }

    public function test_a_regular_user_does_not_see_the_admin_panel_link(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'usuario']));

        $this->get('/')->assertOk()->assertDontSee('Panel de administración');
    }

    public function test_a_guest_does_not_see_the_admin_panel_link(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Panel de administración');
    }
}
