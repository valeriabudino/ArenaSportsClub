<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<x-auth.card
    eyebrow="Último paso"
    title="Verificá tu email"
    subtitle="Te mandamos un link a tu email para confirmar tu cuenta. Si no te llegó, te mandamos otro."
    side-title="Ya casi estás"
    side-text="Verificá tu email y empezá a reservar tus turnos.">

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl bg-lime-50 border border-lime-200 px-4 py-3 text-sm font-bold text-lime-800">
            Te mandamos un link nuevo al email con el que te registraste.
        </div>
    @endif

    <div class="space-y-4">
        <button type="button" wire:click="sendVerification" wire:loading.attr="disabled"
            class="w-full bg-lime-400 text-black py-3 rounded-xl font-black uppercase hover:bg-lime-300 transition disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="sendVerification">Reenviar email</span>
            <span wire:loading wire:target="sendVerification">Enviando...</span>
        </button>

        <button type="button" wire:click="logout"
            class="w-full py-3 rounded-xl font-black uppercase border border-gray-300 text-[#07110d] hover:bg-gray-100 transition">
            Cerrar sesión
        </button>
    </div>
</x-auth.card>
