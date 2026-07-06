<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('home', absolute: false), navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center justify-center bg-[#07110d] px-6 py-10">

    <div class="w-full max-w-5xl bg-white rounded-[2rem] overflow-hidden shadow-2xl grid md:grid-cols-2">

        {{-- Branding --}}
        <div class="hidden md:flex relative bg-cover bg-center"
             style="background-image: url('{{ asset('images/hero-arena.jpg') }}')">

            <div class="absolute inset-0 bg-black/60"></div>

            <div class="relative z-10 p-10 flex flex-col justify-end text-white">

                <div class="inline-flex items-center gap-3 mb-4">

                    <div class="h-12 w-12 rounded-full bg-lime-400 flex items-center justify-center text-[#07110d] shadow-lg">

                        <svg 
                            class="h-7 w-7"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24">

                            <circle cx="12" cy="12" r="9"/>

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 7l4 3-1.5 5h-5L8 10l4-3z"/>

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 10l-4-1M16 10l4-1M9.5 15l-2 4M14.5 15l2 4"/>
                        </svg>

                    </div>


                    <h2 class="text-2xl font-black uppercase">
                        Arena<span class="text-lime-400">Sports</span>Club
                    </h2>

                </div>


                <p class="text-white/80 max-w-xs">
                    Creá tu cuenta y empezá a reservar tus canchas favoritas.
                </p>

            </div>

        </div>



        {{-- Formulario --}}
        <div class="p-10">

            <a href="{{ route('home') }}"
               wire:navigate
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-lime-500 mb-6">

                ← Volver al inicio

            </a>


            <h1 class="text-3xl font-black uppercase text-[#07110d]">
                Crear cuenta
            </h1>


            <p class="text-gray-500 mt-2">
                Unite a ArenaSportsClub
            </p>



            <form wire:submit="register" class="mt-8 space-y-4">


                {{-- Nombre --}}
                <div>

                    <label class="font-bold text-sm">
                        Nombre
                    </label>


                    <input
                        wire:model="name"
                        type="text"
                        required
                        autofocus
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                    >


                    <x-input-error :messages="$errors->get('name')" class="mt-2" />

                </div>




                {{-- Email --}}
                <div>

                    <label class="font-bold text-sm">
                        Email
                    </label>


                    <input
                        wire:model="email"
                        type="email"
                        required
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                    >


                    <x-input-error :messages="$errors->get('email')" class="mt-2" />

                </div>




                {{-- Teléfono --}}
                <div>

                    <label class="font-bold text-sm">
                        WhatsApp
                    </label>


                    <input
                        wire:model="phone"
                        type="tel"
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                    >


                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />

                </div>





                {{-- Password --}}
                <div>

                    <label class="font-bold text-sm">
                        Contraseña
                    </label>


                    <input
                        wire:model="password"
                        type="password"
                        required
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                    >


                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                </div>




                {{-- Confirm Password --}}
                <div>

                    <label class="font-bold text-sm">
                        Confirmar contraseña
                    </label>


                    <input
                        wire:model="password_confirmation"
                        type="password"
                        required
                        class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                    >


                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

                </div>




                <button
                    type="submit"
                    class="w-full bg-lime-400 py-3 rounded-xl font-black uppercase hover:bg-lime-300 transition">

                    Registrarme

                </button>


            </form>



            <p class="mt-6 text-center text-sm text-gray-500">

                ¿Ya tenés cuenta?

                <a href="{{ route('login') }}"
                   wire:navigate
                   class="font-bold text-lime-500">

                    Iniciar sesión

                </a>

            </p>

        </div>

    </div>

</div>
