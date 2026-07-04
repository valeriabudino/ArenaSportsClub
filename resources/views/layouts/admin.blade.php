<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ArenaSportsClub Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="bg-gray-100">


    <div class="min-h-screen flex">


        {{-- Sidebar --}}
        <aside class="w-72 bg-[#07110d] text-white flex flex-col sticky top-0 h-screen">


            {{-- Logo --}}
            <div class="p-6 border-b border-white/10">

                <h1 class="text-2xl font-black">
                    Arena<span class="text-lime-400">Sports</span>
                </h1>

                <p class="text-sm text-white/50 mt-1">
                    Panel administrador
                </p>

            </div>



            {{-- Navegación --}}
            <nav class="flex-1 p-5 space-y-3">


                <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-3 rounded-xl hover:bg-lime-400 hover:text-black font-bold transition">

                    Dashboard

                </a>


                <a href="{{ route('admin.courts') }}"
                    class="block px-4 py-3 rounded-xl hover:bg-lime-400 hover:text-black font-bold transition">

                    Canchas

                </a>



                <a href="{{ route('admin.sports') }}"
                    class="block px-4 py-3 rounded-xl hover:bg-lime-400 hover:text-black font-bold transition">

                    Deportes

                </a>



                <a href="{{ route('admin.turns.index') }}"
                    class="block px-4 py-3 rounded-xl hover:bg-lime-400 hover:text-black font-bold transition">

                    Turnos

                </a>



                <a href="{{ route('admin.club') }}"
                    class="block px-4 py-3 rounded-xl hover:bg-lime-400 hover:text-black font-bold transition">

                    Club

                </a>


            </nav>




            {{-- Usuario --}}
            <div class="p-5 border-t border-white/10 space-y-4">

                <div>
                    <p class="text-sm text-white/50">
                        Administrador
                    </p>

                    <p class="font-bold">
                        {{ auth()->user()->name }}
                    </p>
                </div>

                <a href="{{ route('home') }}"
                    class="block text-center px-4 py-3 rounded-xl border border-white/10 text-white hover:border-lime-400 hover:text-lime-400 font-bold transition">
                    Ver sitio
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="w-full px-4 py-3 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white font-bold transition">
                        Cerrar sesión
                    </button>
                </form>

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


</body>

</html>
