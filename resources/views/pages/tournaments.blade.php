@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <section class="min-h-screen bg-[#07110d] text-white px-6 pt-32 pb-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-lime-400 font-black uppercase tracking-widest">
                    Competí y disfrutá
                </span>

                <h1 class="text-4xl md:text-6xl font-black uppercase mt-4">
                    Torneos
                </h1>

                <p class="text-white/70 mt-6">
                    Participá en competencias organizadas por ArenaSportsClub.
                    Próximamente vas a poder inscribirte, consultar fechas y seguir resultados.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <article class="bg-white text-[#07110d] rounded-3xl overflow-hidden shadow-2xl">
                    <div class="h-52 bg-[#101814] flex items-center justify-center text-lime-400 font-black text-5xl">
                        5v5
                    </div>

                    <div class="p-6">
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-black">
                            Fútbol
                        </span>

                        <h2 class="text-2xl font-black mt-4">
                            Copa Fútbol 5
                        </h2>

                        <p class="text-gray-600 mt-3">
                            Torneo rápido para equipos de fútbol 5 con fase de grupos y eliminación directa.
                        </p>

                        <button class="mt-6 w-full bg-lime-400 text-black py-3 rounded-xl font-black hover:bg-lime-300 transition">
                            Próximamente
                        </button>
                    </div>
                </article>

                <article class="bg-white text-[#07110d] rounded-3xl overflow-hidden shadow-2xl">
                    <div class="h-52 bg-[#101814] flex items-center justify-center text-lime-400 font-black text-5xl">
                        PÁDEL
                    </div>

                    <div class="p-6">
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-black">
                            Pádel
                        </span>

                        <h2 class="text-2xl font-black mt-4">
                            Torneo Pádel Amateur
                        </h2>

                        <p class="text-gray-600 mt-3">
                            Competencia por parejas pensada para jugadores recreativos y nivel inicial.
                        </p>

                        <button class="mt-6 w-full bg-lime-400 text-black py-3 rounded-xl font-black hover:bg-lime-300 transition">
                            Próximamente
                        </button>
                    </div>
                </article>

                <article class="bg-white text-[#07110d] rounded-3xl overflow-hidden shadow-2xl">
                    <div class="h-52 bg-[#101814] flex items-center justify-center text-lime-400 font-black text-5xl">
                        7v7
                    </div>

                    <div class="p-6">
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-black">
                            Fútbol
                        </span>

                        <h2 class="text-2xl font-black mt-4">
                            Liga Fútbol 7
                        </h2>

                        <p class="text-gray-600 mt-3">
                            Liga semanal con tabla de posiciones, resultados y fixture por fechas.
                        </p>

                        <button class="mt-6 w-full bg-lime-400 text-black py-3 rounded-xl font-black hover:bg-lime-300 transition">
                            Próximamente
                        </button>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <x-public.footer />
@endsection