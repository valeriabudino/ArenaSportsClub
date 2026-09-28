<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ArenaSportsClub Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>


<body class="bg-gray-100">


    <div class="min-h-screen flex">


        {{-- Sidebar --}}
        <aside class="w-72 bg-[#07110d] text-white flex flex-col sticky top-0 h-screen">


            {{-- Logo --}}
            <div class="p-6 border-b border-white/10 shrink-0">

                <h1 class="text-2xl font-black">
                    Arena<span class="text-lime-400">Sports</span>
                </h1>

                <p class="text-sm text-white/50 mt-1">
                    Panel administrador
                </p>

            </div>



            {{-- Navegación --}}
            <nav class="flex-1 min-h-0 overflow-y-auto scrollbar-panel p-5 space-y-3">

                @php
                    // [ruta, patrón para marcar la sección activa, texto]
                    $adminLinks = [
                        ['admin.dashboard', 'admin.dashboard', 'Dashboard'],
                        ['admin.courts', 'admin.courts', 'Canchas'],
                        ['admin.sports', 'admin.sports', 'Deportes'],
                        ['admin.turns.index', 'admin.turns.*', 'Turnos'],
                        ['admin.refunds', 'admin.refunds', 'Reembolsos'],
                        ['admin.access-logs.index', 'admin.access-logs.*', 'Accesos'],
                        ['admin.reviews.index', 'admin.reviews.*', 'Comentarios'],
                        ['admin.club', 'admin.club', 'Club'],
                    ];
                @endphp

                @foreach ($adminLinks as [$route, $pattern, $label])
                    @php($active = request()->routeIs($pattern))

                    <a href="{{ route($route) }}"
                        @if ($active) aria-current="page" @endif
                        @class([
                            'block px-4 py-3 rounded-xl font-bold transition',
                            'bg-lime-400 text-black' => $active,
                            'hover:bg-lime-400 hover:text-black' => ! $active,
                        ])>
                        {{ $label }}
                    </a>
                @endforeach

            </nav>




            {{-- Usuario --}}
            <div class="p-5 border-t border-white/10 shrink-0"
                x-data="{ open: false }"
                x-on:click.outside="open = false"
                x-on:keydown.escape.window="open = false">

                <div class="relative flex items-center justify-between gap-3">

                    <div class="min-w-0">
                        <p class="text-sm text-white/50">
                            Administrador
                        </p>

                        <p class="font-bold truncate">
                            {{ auth()->user()->name }}
                        </p>
                    </div>

                    {{-- Engranaje: abre el menu de cuenta --}}
                    <button type="button"
                        x-on:click="open = !open"
                        :aria-expanded="open"
                        aria-haspopup="menu"
                        aria-label="Opciones de cuenta"
                        class="shrink-0 p-2 rounded-xl hover:text-lime-400 hover:bg-white/5 transition"
                        :class="open ? 'text-lime-400 bg-white/5' : 'text-white/70'">
                        <svg class="h-6 w-6 transition-transform duration-300" :class="open && 'rotate-90'"
                            fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>

                    {{-- Menu de cuenta: se abre hacia arriba porque esta al pie del panel --}}
                    <div x-cloak x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        role="menu"
                        class="absolute bottom-full right-0 mb-3 w-full p-2 space-y-1 rounded-xl bg-[#0d1c16] border border-white/10 shadow-2xl">

                        <a href="{{ route('home') }}" role="menuitem"
                            class="block px-4 py-3 rounded-lg text-white hover:bg-white/5 hover:text-lime-400 font-bold transition">
                            Ver sitio
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" role="menuitem"
                                class="w-full text-left px-4 py-3 rounded-lg text-red-400 hover:bg-red-500 hover:text-white font-bold transition">
                                Cerrar sesión
                            </button>
                        </form>

                    </div>

                </div>

            </div>


        </aside>




        {{-- Contenido --}}
        <main class="flex-1">

            <div class="p-8">

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot }}
                @endif

            </div>

        </main>


    </div>

    <x-confirm-dialog />

    @livewireScripts

</body>

</html>
