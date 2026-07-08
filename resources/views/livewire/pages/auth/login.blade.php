<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        if (auth()->user()->role === 'admin') {
            $this->redirect(route('admin.dashboard'), navigate: true);
            return;
        }

        $this->redirect(route('home'), navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-[#07110d] px-6 py-10">
    <div class="w-full max-w-6xl bg-white rounded-[2rem] overflow-hidden shadow-2xl grid lg:grid-cols-2">

        {{-- Branding --}}
        <div class="hidden lg:flex relative bg-cover bg-center min-h-[680px]"
             style="background-image: url('{{ asset('images/hero-arena.jpg') }}')">

            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-black/30"></div>

            <div class="relative z-10 p-10 flex flex-col justify-between text-white">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-lime-400 flex items-center justify-center text-[#07110d] shadow-lg">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7l4 3-1.5 5h-5L8 10l4-3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10l-4-1M16 10l4-1M9.5 15l-2 4M14.5 15l2 4" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-black uppercase">
                        Arena<span class="text-lime-400">Sports</span>Club
                    </h2>
                </a>

                <div>
                    <span class="text-lime-400 font-black uppercase tracking-widest">
                        Reservas deportivas
                    </span>

                    <h3 class="text-5xl font-black uppercase mt-4 leading-tight">
                        Volvé a jugar sin perder tiempo
                    </h3>

                    <p class="mt-5 text-white/80 max-w-md">
                        Iniciá sesión para reservar canchas, consultar tus turnos y gestionar tus reservas.
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="p-8 md:p-12 flex flex-col justify-center">
            <a href="{{ route('home') }}" wire:navigate
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-lime-500 mb-8">
                ← Volver al inicio
            </a>

            <div class="mb-8">
                <span class="text-lime-500 font-black uppercase tracking-widest">
                    Bienvenido
                </span>

                <h1 class="text-4xl font-black uppercase text-[#07110d] mt-2">
                    Iniciar sesión
                </h1>

                <p class="text-gray-500 mt-3">
                    Accedé a tu cuenta para continuar con tus reservas.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-5">
                {{-- Email --}}
                <div>
                    <label class="font-bold text-sm text-gray-700">
                        Email
                    </label>

                    <input
                        wire:model="form.email"
                        id="email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="tuemail@ejemplo.com"
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                    >

                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label class="font-bold text-sm text-gray-700">
                        Contraseña
                    </label>

                    <div class="relative mt-2">
                        <input
                            wire:model="form.password"
                            id="password"
                            :type="show ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="Ingresá tu contraseña"
                            class="w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400 pr-12"
                        >

                        <button
                            type="button"
                            x-on:click="show = !show"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-lime-500 transition"
                        >
                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.6 10.6A3 3 0 0012 15a3 3 0 002.4-4.8" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 5.4A10.8 10.8 0 0112 5c6 0 10 7 10 7a16 16 0 01-3.1 3.9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.1C3.5 7.8 2 12 2 12s4 7 10 7a10.8 10.8 0 004.2-.9" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between gap-4">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input
                            wire:model="form.remember"
                            type="checkbox"
                            class="rounded text-lime-400 focus:ring-lime-400"
                        >

                        Recordarme
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate
                           class="text-sm font-bold text-gray-500 hover:text-lime-500">
                            Olvidé mi contraseña
                        </a>
                    @endif
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-lime-400 text-black py-3 rounded-xl font-black uppercase hover:bg-lime-300 transition disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="login">
                        Entrar
                    </span>

                    <span wire:loading wire:target="login">
                        Ingresando...
                    </span>
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-gray-500">
                ¿No tenés cuenta?

                <a href="{{ route('register') }}" wire:navigate class="font-black text-lime-500 hover:text-lime-600">
                    Registrate
                </a>
            </p>
        </div>
    </div>
</div>