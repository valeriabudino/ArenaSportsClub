<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('home', absolute: false), navigate: true);
    }
}; ?>

<x-auth.card
    eyebrow="Zona segura"
    title="Confirmá tu contraseña"
    subtitle="Por seguridad, ingresá tu contraseña antes de continuar."
    side-title="Tu cuenta, protegida"
    side-text="Te pedimos la contraseña antes de acciones importantes.">

    <form wire:submit="confirmPassword" class="space-y-5">
        <x-auth.password-input wire:model="password" id="password" label="Contraseña"
            placeholder="Ingresá tu contraseña" autocomplete="current-password" autofocus error="password" />

        <button type="submit" wire:loading.attr="disabled"
            class="w-full bg-lime-400 text-black py-3 rounded-xl font-black uppercase hover:bg-lime-300 transition disabled:opacity-60 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="confirmPassword">Confirmar</span>
            <span wire:loading wire:target="confirmPassword">Confirmando...</span>
        </button>
    </form>
</x-auth.card>
