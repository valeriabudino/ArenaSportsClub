<section class="relative min-h-screen flex items-center bg-cover bg-center overflow-hidden"
    style="background-image: url('{{ asset('images/hero-arena.jpg') }}')">
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
                <a href="{{ route('courts.index') }}"
                    class="bg-lime-400 text-black px-8 py-4 rounded-xl font-black uppercase hover:bg-lime-300 transition">
                    Reservar ahora
                </a>

                <a href="#canchas"
                    class="border border-white/40 text-white px-8 py-4 rounded-xl font-black uppercase hover:border-lime-400 hover:text-lime-400 transition">
                    Ver canchas
                </a>
            </div>
        </div>
    </div>
</section>
