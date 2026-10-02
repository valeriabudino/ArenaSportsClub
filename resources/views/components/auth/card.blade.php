{{--
    Tarjeta de las pantallas de autenticacion, con el mismo diseño que el
    login: foto con el logo a la izquierda y el formulario a la derecha.

    <x-auth.card eyebrow="Recuperar acceso" title="Olvidé mi contraseña"
        subtitle="Te mandamos un link para elegir una nueva."
        side-title="Volvé a la cancha" side-text="...">
        ...formulario...
    </x-auth.card>
--}}
@props([
    'eyebrow' => '',
    'title' => '',
    'subtitle' => '',
    'sideTitle' => 'Volvé a jugar sin perder tiempo',
    'sideText' => 'Reservá canchas, consultá tus turnos y gestioná tus reservas.',
    'backUrl' => null,
    'backText' => 'Volver al inicio',
])

<div class="min-h-screen flex items-center justify-center bg-[#07110d] px-6 py-10">
    <div class="w-full max-w-6xl bg-white rounded-[2rem] overflow-hidden shadow-2xl grid lg:grid-cols-2">

        {{-- Branding --}}
        <div class="hidden lg:flex relative bg-cover bg-center min-h-[600px]"
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
                        {{ $sideTitle }}
                    </h3>

                    <p class="mt-5 text-white/80 max-w-md">
                        {{ $sideText }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Formulario --}}
        <div class="p-8 md:p-12 flex flex-col justify-center">
            <a href="{{ $backUrl ?? route('home') }}" wire:navigate
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-lime-500 mb-8">
                ← {{ $backText }}
            </a>

            <div class="mb-8">
                <span class="text-lime-500 font-black uppercase tracking-widest">
                    {{ $eyebrow }}
                </span>

                <h1 class="text-4xl font-black uppercase text-[#07110d] mt-2">
                    {{ $title }}
                </h1>

                @if ($subtitle)
                    <p class="text-gray-500 mt-3">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            {{ $slot }}
        </div>
    </div>
</div>
