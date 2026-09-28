<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Sport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * Las tablas de canchas y deportes del admin tienen que reflejar las altas,
 * ediciones y bajas en el mismo request de Livewire, sin recargar la pagina.
 */
class AdminCourtsAndSportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    private function makeCourt(string $name, ?Sport $sport = null): Court
    {
        return Court::create([
            'sport_id' => ($sport ?? Sport::create(['name' => 'Fútbol']))->id,
            'name' => $name,
            'price_per_hour' => 5000,
        ]);
    }

    public function test_deleted_court_disappears_from_the_list_without_reloading(): void
    {
        $court = $this->makeCourt('Cancha Para Borrar');

        Volt::test('admin.courts.index')
            ->assertSee('Cancha Para Borrar')
            ->call('delete', $court->id)
            ->assertDontSee('Cancha Para Borrar');
    }

    public function test_created_court_appears_in_the_list_without_reloading(): void
    {
        $sport = Sport::create(['name' => 'Pádel']);

        Volt::test('admin.courts.index')
            ->assertDontSee('Cancha Recien Creada')
            ->set('sport_id', $sport->id)
            ->set('name', 'Cancha Recien Creada')
            ->set('price_per_hour', 8000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Cancha Recien Creada');
    }

    public function test_edited_court_shows_its_new_data_without_reloading(): void
    {
        $court = $this->makeCourt('Nombre Viejo');

        Volt::test('admin.courts.index')
            ->call('edit', $court->id)
            ->set('name', 'Nombre Nuevo')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Nombre Nuevo')
            ->assertDontSee('Nombre Viejo');
    }

    public function test_deleted_sport_disappears_from_the_list_without_reloading(): void
    {
        $sport = Sport::create(['name' => 'Deporte Para Borrar']);

        Volt::test('admin.sports.index')
            ->assertSee('Deporte Para Borrar')
            ->call('delete', $sport->id)
            ->assertDontSee('Deporte Para Borrar');
    }

    public function test_created_sport_appears_in_the_list_without_reloading(): void
    {
        Volt::test('admin.sports.index')
            ->assertDontSee('Deporte Recien Creado')
            ->set('name', 'Deporte Recien Creado')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Deporte Recien Creado');
    }
}
