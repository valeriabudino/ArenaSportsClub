<header class="absolute top-0 left-0 w-full z-20">
    <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 lg:px-8 py-6">
        <a href="{{ route('home') }}" class="text-2xl font-black text-white tracking-wide">
            Arena<span class="text-lime-400">Sports</span>Club
        </a>

        <div class="hidden md:flex gap-8 text-white font-semibold">
            <a href="{{ route('home') }}" class="hover:text-lime-400 transition">Inicio</a>
            <a href="{{ route('courts.index') }}" class="hover:text-lime-400 transition">Canchas</a>
            <a href="{{ route('reservations.index') }}" class="hover:text-lime-400 transition">Reservas</a>
            <a href="{{ route('tournaments.index') }}" class="hover:text-lime-400 transition">Torneos</a>
            <a href="{{ route('about') }}" class="hover:text-lime-400 transition">Nosotros</a>
            <a href="{{ route('contact') }}" class="hover:text-lime-400 transition">Contacto</a>
        </div>

        @guest
            <div class="hidden md:flex gap-3">
                <a href="{{ route('login') }}"
                    class="text-white px-5 py-2 rounded-md border border-white/40 hover:border-lime-400 transition font-bold">
                    Iniciar sesión
                </a>

                <a href="{{ route('register') }}"
                    class="bg-lime-400 text-black px-5 py-2 rounded-md font-black hover:bg-lime-300 transition">
                    Registrarse
                </a>
            </div>
        @endguest

        @auth
            <div class="relative group hidden md:block">
                <button
                    class="flex items-center gap-2 text-white px-5 py-2 rounded-md border border-white/40 hover:border-lime-400 transition font-bold">
                    {{ auth()->user()->name }}
                    <span>▾</span>
                </button>

                <div
                    class="absolute right-0 top-full pt-2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition">
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                        <a href="{{ route('reservations.index') }}"
                            class="block px-5 py-3 text-sm font-bold text-gray-700 hover:bg-lime-50">
                            Mis reservas
                        </a>

                        <a href="{{ route('profile') }}"
                            class="block px-5 py-3 text-sm font-bold text-gray-700 hover:bg-lime-50">
                            Mi perfil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-5 py-3 text-sm font-bold text-red-600 hover:bg-red-50">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth
    </nav>
</header>
