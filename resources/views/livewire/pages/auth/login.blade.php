<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-[#07110d] px-6">

    <div class="w-full max-w-5xl bg-white rounded-[2rem] overflow-hidden shadow-2xl grid md:grid-cols-2">

        {{-- Imagen / Branding --}}
        <div class="hidden md:flex relative bg-cover bg-center"
            style="background-image: url('{{ asset('images/hero-arena.jpg') }}')">

            <div class="absolute inset-0 bg-black/60"></div>

            <div class="relative z-10 p-10 flex flex-col justify-end text-white">

                <div class="inline-flex items-center gap-2 mb-4">
                    <div
                        class="h-12 w-12 rounded-full bg-lime-400 flex items-center justify-center text-[#07110d] shadow-lg">

                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9" />

                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7l4 3-1.5 5h-5L8 10l4-3z" />

                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 10l-4-1M16 10l4-1M9.5 15l-2 4M14.5 15l2 4" />
                        </svg>

                    </div>

                    <h2 class="text-2xl font-black uppercase">
                        Arena<span class="text-lime-400">Sports</span>Club
                    </h2>
                </div>

                <p class="text-white/80 max-w-xs">
                    Reservá tu cancha, asegurá tu turno y disfrutá del juego.
                </p>

            </div>

        </div>



        {{-- Formulario --}}
        <div class="p-10">
            <a href="{{ route('home') }}" wire:navigate
                class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-lime-500 mb-6">
                ← Volver al inicio
            </a>

            <h1 class="text-3xl font-black uppercase text-[#07110d]">
                Iniciar sesión
            </h1>

            <p class="text-gray-500 mt-2">
                Bienvenido nuevamente jugador
            </p>


            <x-auth-session-status class="mb-4 mt-6" :status="session('status')" />


            <form wire:submit="login" class="mt-8 space-y-5">


                {{-- Email --}}
                <div>

                    <label class="font-bold text-sm">
                        Email
                    </label>

                    <input wire:model="form.email" id="email" type="email" required autofocus
                        autocomplete="username"
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">

                    <x-input-error :messages="$errors->get('form.email')" class="mt-2" />

                </div>



                {{-- Password --}}
                <div>

                    <label class="font-bold text-sm">
                        Contraseña
                    </label>

                    <input wire:model="form.password" id="password" type="password" required
                        autocomplete="current-password"
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">

                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />

                </div>



                {{-- Remember --}}
                <label class="flex items-center gap-2 text-sm text-gray-600">

                    <input wire:model="form.remember" type="checkbox" class="rounded text-lime-400 focus:ring-lime-400">

                    Recordarme

                </label>



                <div class="flex items-center justify-between">

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-sm text-gray-500 hover:text-lime-500">

                            Olvidé mi contraseña

                        </a>
                    @endif


                    <button type="submit"
                        class="bg-lime-400 px-8 py-3 rounded-xl font-black uppercase hover:bg-lime-300 transition">

                        Entrar

                    </button>

                </div>


            </form>


            <p class="mt-8 text-center text-sm text-gray-500">

                ¿No tenés cuenta?

                <a href="{{ route('register') }}" wire:navigate class="font-bold text-lime-500">

                    Registrate

                </a>

            </p>

        </div>

    </div>

</div>
