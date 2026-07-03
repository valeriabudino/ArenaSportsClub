@extends('layouts.public')

@section('content')

<header class="absolute top-0 left-0 w-full z-20">
    <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-black text-white tracking-wide">
            Arena<span class="text-lime-400">Sports</span>Club
        </h1>

        <div class="hidden md:flex gap-8 text-white font-semibold">
            <a href="#" class="text-lime-400">Inicio</a>
            <a href="#" class="hover:text-lime-400 transition">Canchas</a>
            <a href="#" class="hover:text-lime-400 transition">Reservas</a>
            <a href="#" class="hover:text-lime-400 transition">Nosotros</a>
            <a href="#" class="hover:text-lime-400 transition">Contacto</a>
        </div>

        <div class="hidden md:flex gap-3">
            <a href="/login"
               class="text-white px-5 py-2 rounded-md border border-white/40 hover:border-lime-400 transition font-bold">
                Iniciar sesión
            </a>

            <a href="/register"
               class="bg-lime-400 text-black px-5 py-2 rounded-md font-black hover:bg-lime-300 transition">
                Registrarse
            </a>
        </div>
    </nav>
</header>

<section
    class="relative min-h-screen flex items-center bg-cover bg-center overflow-hidden"
    style="background-image: url('{{ asset('images/hero-arena.jpg') }}')"
>
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/55 to-black/20"></div>

    <div class="relative z-10 max-w-7xl mx-auto w-full px-6 lg:px-8 pt-24">
        <div class="max-w-2xl">
            <h2 class="text-white text-5xl md:text-7xl font-black italic leading-tight uppercase">
                Tu cancha. <br>
                <span class="text-lime-400">Tu momento.</span> <br>
                Tu juego.
            </h2>

            <p class="text-white/90 mt-6 text-lg md:text-xl max-w-xl">
                Reservá tu cancha de fútbol, pádel y más de forma fácil, rápida y segura.
            </p>

            <div class="flex flex-wrap gap-4 mt-8">
                <a href="#"
                   class="bg-lime-400 text-black px-7 py-4 rounded-md font-black uppercase hover:bg-lime-300 transition">
                    Reservar ahora
                </a>

                <a href="#"
                   class="border border-white/50 text-white px-7 py-4 rounded-md font-black uppercase hover:border-lime-400 hover:text-lime-400 transition">
                    Ver canchas
                </a>
            </div>
        </div>
    </div>
</section>

<section class="relative z-20 max-w-6xl mx-auto px-6 -mt-20">
    <div class="bg-[#07110d] text-white rounded-3xl shadow-2xl grid grid-cols-1 md:grid-cols-5 overflow-hidden">
        <div class="p-6 text-center border-b md:border-b-0 md:border-r border-white/10">
            <div class="text-lime-400 text-3xl mb-3">📅</div>
            <h3 class="font-black uppercase">Reservá 24/7</h3>
            <p class="text-sm text-white/70 mt-2">Hacé tu reserva cuando quieras.</p>
        </div>

        <div class="p-6 text-center border-b md:border-b-0 md:border-r border-white/10">
            <div class="text-lime-400 text-3xl mb-3">🛡️</div>
            <h3 class="font-black uppercase">Pago seguro</h3>
            <p class="text-sm text-white/70 mt-2">Tus pagos protegidos.</p>
        </div>

        <div class="p-6 text-center border-b md:border-b-0 md:border-r border-white/10">
            <div class="text-lime-400 text-3xl mb-3">⏱️</div>
            <h3 class="font-black uppercase">Sin esperas</h3>
            <p class="text-sm text-white/70 mt-2">Reserva en pocos segundos.</p>
        </div>

        <div class="p-6 text-center border-b md:border-b-0 md:border-r border-white/10">
            <div class="text-lime-400 text-3xl mb-3">⭐</div>
            <h3 class="font-black uppercase">Mejores canchas</h3>
            <p class="text-sm text-white/70 mt-2">Espacios en perfecto estado.</p>
        </div>

        <div class="p-6 text-center">
            <div class="text-lime-400 text-3xl mb-3">🎧</div>
            <h3 class="font-black uppercase">Soporte</h3>
            <p class="text-sm text-white/70 mt-2">Estamos para ayudarte.</p>
        </div>
    </div>
</section>

@endsection