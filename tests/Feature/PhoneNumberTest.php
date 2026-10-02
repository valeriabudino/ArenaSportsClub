<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WhatsApp\TwilioWhatsAppSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;
use Twilio\Rest\Client;

class PhoneNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_stores_the_phone_in_whatsapp_format(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Ana')
            ->set('email', 'ana@example.com')
            ->set('phone', '0370 15 412-3456')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasNoErrors();

        $this->assertSame('+5493704123456', User::where('email', 'ana@example.com')->value('phone'));
    }

    public function test_registration_rejects_a_phone_without_area_code(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Ana')
            ->set('email', 'ana@example.com')
            ->set('phone', '4123456')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['phone']);

        $this->assertDatabaseMissing('users', ['email' => 'ana@example.com']);
    }

    public function test_the_profile_stores_the_phone_in_whatsapp_format(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('profile.update-profile-information-form')
            ->set('phone', '370 4123456')
            ->call('updateProfileInformation')
            ->assertHasNoErrors()
            ->assertSet('phone', '+5493704123456');

        $this->assertSame('+5493704123456', $user->fresh()->phone);
    }

    public function test_the_profile_phone_can_be_left_empty(): void
    {
        $user = User::factory()->create(['phone' => '+5493704123456']);

        $this->actingAs($user);

        Volt::test('profile.update-profile-information-form')
            ->set('phone', '')
            ->call('updateProfileInformation')
            ->assertHasNoErrors();

        $this->assertNull($user->fresh()->phone);
    }

    private function fakeTwilio(): object
    {
        $messages = new class {
            public array $sent = [];

            public function create(string $to, array $params): void
            {
                $this->sent[] = $to;
            }
        };

        // Cliente de Twilio sin credenciales ni llamadas reales.
        $client = new class extends Client {
            public $messages;

            public function __construct() {}
        };
        $client->messages = $messages;

        return $client;
    }

    public function test_whatsapp_is_sent_to_the_normalized_number_even_for_old_formats(): void
    {
        $client = $this->fakeTwilio();
        $sender = new TwilioWhatsAppSender($client, '+14155238886');

        // Como estaba guardado el telefono antes de este cambio.
        $this->assertTrue($sender->sendMessage('3704111111', 'Hola'));

        $this->assertSame(['whatsapp:+5493704111111'], $client->messages->sent);
    }

    public function test_whatsapp_is_not_sent_to_an_invalid_number(): void
    {
        $client = $this->fakeTwilio();
        $sender = new TwilioWhatsAppSender($client, '+14155238886');

        $this->assertFalse($sender->sendMessage('4123456', 'Hola'));

        $this->assertSame([], $client->messages->sent);
    }
}
