@php
    // Datos reales (antes eran numeros fijos). La calificacion muestra "Nuevo"
    // mientras no haya ninguna resena, en vez de un 0.
    $clients = \App\Models\User::where('role', '!=', 'admin')->count();
    $courts = \App\Models\Court::where('is_active', true)->count();
    $bookings = \App\Models\Turn::where('status', 'booked')->count();
    $rating = \App\Models\CourtReview::avg('rating');
@endphp

<section class="bg-[#f5f5f5] pb-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">

        <div class="bg-[#07110d] rounded-3xl px-8 py-8 grid grid-cols-2 md:grid-cols-4 text-white shadow-2xl overflow-hidden">

            <div class="flex items-center justify-center gap-4 border-r border-white/10">
                <svg class="h-12 w-12 text-lime-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21a6 6 0 00-10 0"/>
                    <circle cx="12" cy="9" r="4"/>
                </svg>

                <div>
                    <h3 class="text-4xl font-black">{{ $clients }}</h3>
                    <p class="text-sm text-white/70">
                        Clientes registrados
                    </p>
                </div>
            </div>


            <div class="flex items-center justify-center gap-4 border-r border-white/10">
                <svg class="h-12 w-12 text-lime-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M12 5v14M3 12h18"/>
                </svg>

                <div>
                    <h3 class="text-4xl font-black">{{ $courts }}</h3>
                    <p class="text-sm text-white/70">
                        Canchas disponibles
                    </p>
                </div>
            </div>


            <div class="flex items-center justify-center gap-4 border-r border-white/10">
                <svg class="h-12 w-12 text-lime-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="4" y="5" width="16" height="16" rx="2"/>
                    <path d="M16 3v4M8 3v4M4 11h16"/>
                </svg>

                <div>
                    <h3 class="text-4xl font-black">{{ $bookings }}</h3>
                    <p class="text-sm text-white/70">
                        Reservas realizadas
                    </p>
                </div>
            </div>


            <div class="flex items-center justify-center gap-4">
                <svg class="h-12 w-12 text-lime-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 3l2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9L12 3z"/>
                </svg>

                <div>
                    <h3 class="text-4xl font-black">{{ $rating ? number_format($rating, 1, ',', '.') : 'Nuevo' }}</h3>
                    <p class="text-sm text-white/70">
                        Calificación promedio
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>