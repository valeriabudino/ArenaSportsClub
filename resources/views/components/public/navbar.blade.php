<header class="absolute top-0 left-0 w-full z-20" x-data="{ mobileOpen: false }">
    <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 lg:px-8 py-6">
        <a href="{{ route('home') }}" class="text-2xl font-black text-white tracking-wide">
            Arena<span class="text-lime-400">Sports</span>Club
        </a>

        <div class="hidden md:flex gap-8 text-white font-semibold">
            <a href="{{ route('home') }}" class="hover:text-lime-400 transition">Inicio</a>
            <a href="{{ route('courts.index') }}" class="hover:text-lime-400 transition">Canchas</a>
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

        {{-- Boton hamburguesa, solo mobile --}}
        <button
            type="button"
            x-on:click="mobileOpen = !mobileOpen"
            class="md:hidden text-white p-2 -mr-2"
            :aria-expanded="mobileOpen"
            aria-label="Abrir menú">
            <svg x-cloak x-show="!mobileOpen" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-cloak x-show="mobileOpen" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </nav>

    {{-- Panel mobile --}}
    <div x-cloak x-show="mobileOpen" x-on:click.outside="mobileOpen = false"
        class="md:hidden mx-4 mt-2 rounded-2xl bg-[#07110d] border border-white/10 shadow-2xl overflow-hidden">
        <div class="flex flex-col text-white font-semibold divide-y divide-white/10">
            <a href="{{ route('home') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Inicio</a>
            <a href="{{ route('courts.index') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Canchas</a>
            <a href="{{ route('tournaments.index') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Torneos</a>
            <a href="{{ route('about') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Nosotros</a>
            <a href="{{ route('contact') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Contacto</a>

            @guest
                <a href="{{ route('login') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="px-6 py-4 text-lime-400 hover:bg-white/5 transition">Registrarse</a>
            @endguest

            @auth
                <a href="{{ route('reservations.index') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Mis reservas</a>
                <a href="{{ route('profile') }}" class="px-6 py-4 hover:bg-white/5 hover:text-lime-400 transition">Mi perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-6 py-4 text-red-400 hover:bg-white/5 transition">
                        Cerrar sesión
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>
