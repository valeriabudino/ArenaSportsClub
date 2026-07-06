@extends('layouts.public')

@section('content')
    <x-public.navbar />

    <section class="min-h-screen bg-[#07110d] text-white px-6 pt-32 pb-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-lime-400 font-black uppercase tracking-widest">
                    Nosotros
                </span>

                <h1 class="text-4xl md:text-6xl font-black uppercase mt-4">
                    El equipo detrás de ArenaSportsClub
                </h1>

                <p class="text-white/70 mt-6">
                    Proyecto académico desarrollado para digitalizar la gestión de canchas,
                    reservas, turnos y experiencia de usuarios en complejos deportivos.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                {{-- Card 1 --}}
                <article class="bg-white text-[#07110d] rounded-3xl p-8 shadow-2xl hover:-translate-y-2 transition">
                    <div class="h-28 w-28 rounded-full mx-auto mb-6 overflow-hidden">
                        <img src="{{ asset('images/team/nadia.png') }}" alt="Medina Nadia"
                            class="h-full w-full object-cover">
                    </div>

                    <h2 class="text-2xl font-black text-center">
                        Medina Nadia
                    </h2>

                    <p class="text-lime-600 font-black text-center mt-1">
                        Backend / Gestion de datos
                    </p>

                    <p class="text-gray-600 text-center mt-4">
                        Encargada del desarrollo de funcionalidades internas, gestión de datos y validación de procesos para
                        el correcto funcionamiento del sistema.
                    </p>

                    <div class="flex flex-wrap justify-center gap-2 mt-5">
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">Laravel</span>
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">SQLite</span>
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">PHP</span>
                    </div>

                    <div class="flex justify-center gap-3 mt-6">
                        <a href="https://github.com/nadiamc" target="_blank" rel="noopener noreferrer"
                            class="font-black text-sm hover:text-lime-600">
                            GitHub
                        </a>

                        <a href="https://www.linkedin.com/in/nadia-cm/" target="_blank" rel="noopener noreferrer"
                            class="font-black text-sm hover:text-lime-600">
                            LinkedIn
                        </a>
                    </div>
                </article>

                {{-- Card 2 --}}
                <article class="bg-white text-[#07110d] rounded-3xl p-8 shadow-2xl hover:-translate-y-2 transition">
                    <div class="h-28 w-28 rounded-full mx-auto mb-6 overflow-hidden">
                        <img src="{{ asset('images/team/valeria.png') }}" alt="Budiño Valeria"
                            class="h-full w-full object-cover">
                    </div>

                    <h2 class="text-2xl font-black text-center">
                        Budiño Valeria
                    </h2>

                    <p class="text-lime-600 font-black text-center mt-1">
                        Backend / Reservas
                    </p>

                    <p class="text-gray-600 text-center mt-4">
                        Responsable de la lógica de reservas, generación de turnos,
                        modelos principales y conexión de funcionalidades del sistema.
                    </p>

                    <div class="flex flex-wrap justify-center gap-2 mt-5">
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">Laravel</span>
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">SQLite</span>
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">PHP</span>
                    </div>

                    <div class="flex justify-center gap-3 mt-6">
                        <a href="https://github.com/valeriabudino" target="_blank" rel="noopener noreferrer"
                            class="font-black text-sm hover:text-lime-600">
                            GitHub
                        </a>

                        <a href="https://www.linkedin.com/in/valeria-budino-fernandez/" target="_blank"
                            rel="noopener noreferrer" class="font-black text-sm hover:text-lime-600">
                            LinkedIn
                        </a>
                    </div>
                </article>

                {{-- Card 3 --}}
                <article class="bg-white text-[#07110d] rounded-3xl p-8 shadow-2xl hover:-translate-y-2 transition">
                    <div class="h-28 w-28 rounded-full mx-auto mb-6 overflow-hidden">
                        <img src="{{ asset('images/team/matias.png') }}" alt="Matías Nuñez"
                            class="h-full w-full object-cover">
                    </div>

                    <h2 class="text-2xl font-black text-center">
                        Nuñez Matias
                    </h2>

                    <p class="text-lime-600 font-black text-center mt-1">
                        Frontend / UI
                    </p>

                    <p class="text-gray-600 text-center mt-4">
                        Encargado del diseño visual, estructura pública, experiencia de usuario
                        y adaptación de interfaces con identidad ArenaSportsClub.
                    </p>

                    <div class="flex flex-wrap justify-center gap-2 mt-5">
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">Blade</span>
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">Tailwind</span>
                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">Livewire</span>
                    </div>

                    <div class="flex justify-center gap-3 mt-6">
                        <a href="https://github.com/MatiasDN19" target="_blank" rel="noopener noreferrer"
                            class="font-black text-sm hover:text-lime-600">
                            GitHub
                        </a>

                        <a href="https://www.linkedin.com/in/matias-nu%C3%B1ez-63694a24b/" target="_blank"
                            rel="noopener noreferrer" class="font-black text-sm hover:text-lime-600">
                            LinkedIn
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <x-public.footer />
@endsection
