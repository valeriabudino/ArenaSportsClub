<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\TestCase;

/**
 * Las pantallas de autenticacion estan en español y no muestran claves de
 * traduccion sin resolver (ej. "passwords.sent", "auth.failed").
 */
class AuthScreensTest extends TestCase
{
    use RefreshDatabase;

    private const RAW_KEYS = ['passwords.', 'auth.failed', 'auth.password', 'auth.throttle'];

    public function test_forgot_password_screen_is_in_spanish(): void
    {
        $this->get('/forgot-password')->assertOk()
            ->assertSee('Olvidé mi contraseña')
            ->assertSee('Enviar link');
    }

    public function test_reset_password_screen_is_in_spanish(): void
    {
        $this->get('/reset-password/token?email=ana@example.com')->assertOk()
            ->assertSee('Contraseña nueva')
            ->assertSee('Repetí la contraseña')
            ->assertSee('Guardar contraseña');
    }

    public function test_confirm_password_and_verify_email_screens_are_in_spanish(): void
    {
        $this->actingAs(User::factory()->unverified()->create());

        $this->get('/confirm-password')->assertOk()->assertSee('Confirmá tu contraseña');
        $this->get('/verify-email')->assertOk()->assertSee('Verificá tu email')->assertSee('Reenviar email');
    }

    public function test_sending_the_reset_link_shows_a_translated_message(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'ana@example.com']);

        $component = Volt::test('pages.auth.forgot-password')
            ->set('email', 'ana@example.com')
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors()
            ->assertSee('te enviamos un link');

        foreach (self::RAW_KEYS as $key) {
            $component->assertDontSee($key);
        }
    }

    public function test_an_unknown_email_gets_the_same_message_as_a_registered_one(): void
    {
        // Para no revelar que emails tienen cuenta.
        Volt::test('pages.auth.forgot-password')
            ->set('email', 'no-existe@example.com')
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors()
            ->assertSee('te enviamos un link');
    }

    public function test_a_wrong_password_on_login_shows_a_translated_error(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        Volt::test('pages.auth.login')
            ->set('form.email', 'ana@example.com')
            ->set('form.password', 'incorrecta')
            ->call('login')
            ->assertHasErrors(['form.email'])
            ->assertSee('El email o la contraseña son incorrectos.')
            ->assertDontSee('auth.failed');
    }
}
