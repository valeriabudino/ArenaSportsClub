@php
    $courts = \App\Models\Court::with('sport')->where('is_active', true)->latest()->take(8)->get();
@endphp

<section id="canchas" class="bg-[#f5f5f5] py-28">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <div>
                <span class="text-lime-500 font-black uppercase tracking-widest">
                    Elegí dónde jugar
                </span>

                <h2 class="text-4xl md:text-5xl font-black uppercase mt-3">
                    Nuestras canchas
                </h2>

                <p class="text-gray-600 mt-5 max-w-2xl">
                    Encontrá el espacio perfecto para tu próximo partido.
                    Canchas modernas, iluminadas y listas para competir.
                </p>
            </div>

            <a href="{{ route('courts.index') }}"
                class="border border-lime-400 text-[#07110d] px-6 py-3 rounded-xl font-black uppercase hover:bg-lime-400 transition text-center">
                Ver todas
            </a>
        </div>

        @if ($courts->count())
            <div class="flex gap-8 overflow-x-auto pb-6 snap-x snap-mandatory">
                @foreach ($courts as $court)
                    <article
                        class="min-w-[320px] md:min-w-[360px] bg-white rounded-3xl overflow-hidden shadow-lg hover:-translate-y-2 transition duration-300 snap-start">
                        @if ($court->image)
                            <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}"
                                class="h-56 w-full object-cover">
                        @else
                            <div class="h-56 bg-[#07110d] flex items-center justify-center text-white/50 font-bold">
                                Sin imagen
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h3 class="text-2xl font-black">
                                    {{ $court->name }}
                                </h3>

                                <span class="text-lime-500 font-bold">
                                    {{ $court->sport->name }}
                                </span>
                            </div>

                            <div class="space-y-3 mt-5 text-gray-600">

                                {{-- Capacidad --}}
                                <p class="flex items-center gap-3">
                                    <svg class="h-5 w-5 text-lime-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 21a5 5 0 00-10 0M12 12a4 4 0 100-8 4 4 0 000 8z" />
                                    </svg>

                                    {{ $court->capacity ? 'Hasta ' . $court->capacity . ' jugadores' : 'Capacidad a consultar' }}
                                </p>


                                {{-- Disponible --}}
                                <p class="flex items-center gap-3">
                                    <svg class="h-5 w-5 text-lime-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>

                                    Disponible para reservar
                                </p>


                                {{-- Descripción --}}
                                <p class="flex items-center gap-3">
                                    <svg class="h-5 w-5 text-lime-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6h16M4 12h16M4 18h7" />
                                    </svg>

                                    {{ $court->description ?: 'Cancha profesional' }}
                                </p>

                            </div>

                            <div class="flex justify-between items-center mt-8">
                                <span class="font-black text-xl">
                                    ${{ number_format($court->price_per_hour, 0, ',', '.') }}/h
                                </span>

                                <a href="{{ route('courts.show', $court) }}"
                                    class="bg-lime-400 px-5 py-3 rounded-xl font-bold hover:bg-lime-300 transition">
                                    Ver cancha
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl p-10 text-center shadow">
                <h3 class="text-2xl font-black">Todavía no hay canchas cargadas</h3>
                <p class="text-gray-500 mt-3">
                    Cuando el administrador registre canchas activas, aparecerán acá.
                </p>
            </div>
        @endif
    </div>
</section>
