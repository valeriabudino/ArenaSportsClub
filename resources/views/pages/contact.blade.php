@extends('layouts.public')

@section('content')
    <x-public.navbar />

    @php
        $club = \App\Models\ClubSetting::first();
    @endphp

    <section class="min-h-screen bg-[#07110d] text-white px-6 pt-32 pb-20">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-lime-400 font-black uppercase tracking-widest">
                    Contacto
                </span>

                <h1 class="text-4xl md:text-6xl font-black uppercase mt-4">
                    Hablemos de tu próximo partido
                </h1>

                <p class="text-white/70 mt-6 max-w-xl">
                    Consultá disponibilidad, horarios o cualquier duda sobre nuestras canchas.
                </p>

                <div class="grid gap-5 mt-10">
                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <p class="text-lime-400 font-black">Dirección</p>
                        <p class="text-white/80 mt-1">{{ $club->address ?? 'Formosa, Argentina' }}</p>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <p class="text-lime-400 font-black">WhatsApp</p>
                        <p class="text-white/80 mt-1">{{ $club->phone ?? '+54 370 000 0000' }}</p>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <p class="text-lime-400 font-black">Email</p>
                        <p class="text-white/80 mt-1">{{ $club->email ?? 'arenasportsclub@email.com' }}</p>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-5 border border-white/10">
                        <p class="text-lime-400 font-black">Horarios</p>
                        <p class="text-white/80 mt-1">
                            {{ $club->opening_time ?? '08:00' }} a {{ $club->closing_time ?? '23:00' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-2xl text-[#07110d]">
                <h2 class="text-3xl font-black">
                    Enviá tu consulta
                </h2>

                <p class="text-gray-500 mt-2">
                    Dejanos tus datos y te responderemos a la brevedad.
                </p>

                <form class="mt-8 space-y-5">
                    <div>
                        <label class="font-bold text-sm text-gray-700">Nombre</label>
                        <input type="text" class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
                    </div>

                    <div>
                        <label class="font-bold text-sm text-gray-700">Email</label>
                        <input type="email" class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
                    </div>

                    <div>
                        <label class="font-bold text-sm text-gray-700">Mensaje</label>
                        <textarea rows="5" class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"></textarea>
                    </div>

                    <button type="button" class="w-full bg-lime-400 text-black py-3 rounded-xl font-black hover:bg-lime-300 transition">
                        Enviar mensaje
                    </button>
                </form>
            </div>
        </div>
    </section>

    <x-public.footer />
@endsection