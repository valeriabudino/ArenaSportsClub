<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone = Auth::user()->phone ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-2xl font-black text-[#07110d]">
            Información personal
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            Actualizá tu nombre y correo electrónico.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-5">
        <div>
            <label class="font-bold text-sm text-gray-700">Nombre</label>

            <input
                wire:model="name"
                id="name"
                name="name"
                type="text"
                required
                autofocus
                autocomplete="name"
                class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
            >

            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="font-bold text-sm text-gray-700">Email</label>

            <input
                wire:model="email"
                id="email"
                name="email"
                type="email"
                required
                autocomplete="username"
                class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
            >

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 rounded-xl bg-yellow-50 border border-yellow-200 p-4">
                    <p class="text-sm text-yellow-800">
                        Tu email todavía no fue verificado.

                        <button
                            wire:click.prevent="sendVerification"
                            class="font-bold underline hover:text-yellow-900">
                            Reenviar verificación
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-lime-600">
                            Se envió un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <label class="font-bold text-sm text-gray-700">WhatsApp</label>

            <input
                wire:model="phone"
                id="phone"
                name="phone"
                type="tel"
                autocomplete="tel"
                class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
            >

            <p class="mt-2 text-sm text-gray-500">
                Lo usamos para avisarte por WhatsApp cuando confirmamos tu reserva y para recordarte el turno.
            </p>

            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                Guardar cambios
            </button>

            <x-action-message class="me-3 font-bold text-lime-600" on="profile-updated">
                Guardado.
            </x-action-message>
        </div>
    </form>
</section>
