<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        // Si el email no existe se muestra lo mismo que si se envio, para no
        // revelar que emails tienen cuenta.
        if (! in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER])) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __(Password::RESET_LINK_SENT));
    }
}; ?>

<x-auth.card
    eyebrow="Recuperar acceso"
    title="Olvidé mi contraseña"
    subtitle="Ingresá el email de tu cuenta y te mandamos un link para elegir una contraseña nueva."
    side-title="Volvé a la cancha en un minuto"
    side-text="Recuperá el acceso a tu cuenta y seguí reservando tus turnos."
    :back-url="route('login')"
    back-text="Volver a iniciar sesión">

    <x-auth-session-status class="mb-4 rounded-xl bg-lime-50 border border-lime-200 px-4 py-3 text-lime-800" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-5">
        <div>
            <label for="email" class="font-bold text-sm text-gray-700">
                Email
            </label>

            <input wire:model="email" id="email" type="email" required autofocus autocomplete="username"
                placeholder="tuemail@ejemplo.com"
                class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" wire:loading.attr="disabled"
            class="w-full bg-lime-400 text-black py-3 rounded-xl font-black uppercase hover:bg-lime-300 transition disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="sendPasswordResetLink">Enviar link</span>
            <span wire:loading wire:target="sendPasswordResetLink">Enviando...</span>
        </button>
    </form>
</x-auth.card>
