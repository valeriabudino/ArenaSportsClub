@extends('layouts.admin')

@section('content')

    <div class="py-10 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="mb-8">
                <h1 class="text-3xl font-black text-gray-900">
                    Bienvenido, {{ auth()->user()->name }}
                </h1>

                <p class="text-gray-500 mt-2">
                    Tenés permisos de administrador para gestionar ArenaSportsClub.
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-2xl p-6 shadow">
                    <p class="text-gray-500 text-sm font-bold">Canchas</p>
                    <h3 class="text-3xl font-black text-lime-500 mt-2">{{ $courtsCount }}</h3>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow">
                    <p class="text-gray-500 text-sm font-bold">Turnos</p>
                    <h3 class="text-3xl font-black text-lime-500 mt-2">{{ $turnsCount }}</h3>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow">
                    <p class="text-gray-500 text-sm font-bold">Deportes</p>
                    <h3 class="text-3xl font-black text-lime-500 mt-2">{{ $sportsCount }}</h3>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow">
                    <p class="text-gray-500 text-sm font-bold">Reservas</p>
                    <h3 class="text-3xl font-black text-lime-500 mt-2">{{ $bookedCount }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-black mb-6">
                    Accesos rápidos
                </h2>

                <div class="grid md:grid-cols-4 gap-5">
                    <a href="{{ route('admin.courts') }}"
                       class="p-6 rounded-2xl border hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Canchas</h3>
                        <p class="text-sm text-gray-500 mt-2">Gestionar canchas.</p>
                    </a>

                    <a href="{{ route('admin.sports') }}"
                       class="p-6 rounded-2xl border hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Deportes</h3>
                        <p class="text-sm text-gray-500 mt-2">Gestionar deportes.</p>
                    </a>

                    <a href="{{ route('admin.turns.index') }}"
                       class="p-6 rounded-2xl border hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Turnos</h3>
                        <p class="text-sm text-gray-500 mt-2">Administrar turnos.</p>
                    </a>

                    <a href="{{ route('admin.club') }}"
                       class="p-6 rounded-2xl border hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Club</h3>
                        <p class="text-sm text-gray-500 mt-2">Configurar datos.</p>
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection