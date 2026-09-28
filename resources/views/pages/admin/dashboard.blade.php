@extends('layouts.admin')

@section('content')

    @php
        $money = fn ($amount) => '$' . number_format($amount, 0, ',', '.');

        // Iconos (heroicons outline)
        $icons = [
            'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5',
            'clock' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            'money' => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
            'card' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z',
            'refund' => 'M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3',
            'courts' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z',
        ];

        $cards = [
            ['label' => 'Reservas de hoy', 'value' => $todayBookings, 'hint' => 'Turnos confirmados para hoy', 'route' => 'admin.turns.index', 'icon' => 'calendar'],
            ['label' => 'Próximas reservas', 'value' => $upcomingBookings, 'hint' => 'Confirmadas de hoy en adelante', 'route' => 'admin.turns.index', 'icon' => 'clock'],
            ['label' => 'Ingresos del mes', 'value' => $money($monthIncome), 'hint' => 'Pagos aprobados de ' . now()->translatedFormat('F'), 'route' => 'admin.turns.index', 'icon' => 'money', 'highlight' => true],
            ['label' => 'Pagos pendientes', 'value' => $pendingPayments, 'hint' => 'Reservas esperando el pago en Mercado Pago', 'route' => 'admin.turns.index', 'icon' => 'card'],
            ['label' => 'Reembolsos pendientes', 'value' => $pendingRefunds, 'hint' => 'Cancelaciones a devolver', 'route' => 'admin.refunds', 'icon' => 'refund', 'warn' => $pendingRefunds > 0],
            ['label' => 'Canchas activas', 'value' => $activeCourts, 'hint' => 'Disponibles para reservar', 'route' => 'admin.courts', 'icon' => 'courts'],
        ];
    @endphp

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

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @foreach ($cards as $card)
                    @php
                        $highlight = $card['highlight'] ?? false;
                        $warn = $card['warn'] ?? false;
                    @endphp

                    <a href="{{ route($card['route']) }}"
                        @class([
                            'group rounded-2xl p-6 border shadow-sm transition hover:shadow-lg hover:-translate-y-0.5',
                            'bg-[#07110d] border-[#07110d]' => $highlight,
                            'bg-white border-gray-200 hover:border-lime-400' => ! $highlight,
                        ])>

                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p @class(['text-sm font-bold', 'text-white/60' => $highlight, 'text-gray-500' => ! $highlight])>
                                    {{ $card['label'] }}
                                </p>

                                <p @class([
                                    'text-3xl font-black mt-2 truncate',
                                    'text-lime-400' => $highlight,
                                    'text-amber-600' => $warn,
                                    'text-[#07110d]' => ! $highlight && ! $warn,
                                ])>
                                    {{ $card['value'] }}
                                </p>
                            </div>

                            <div @class([
                                'shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl',
                                'bg-lime-400/10 text-lime-400' => $highlight,
                                'bg-amber-100 text-amber-600' => $warn,
                                'bg-lime-400/15 text-lime-600' => ! $highlight && ! $warn,
                            ])>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$card['icon']] }}" />
                                </svg>
                            </div>
                        </div>

                        <p @class(['mt-4 text-xs', 'text-white/50' => $highlight, 'text-gray-500' => ! $highlight])>
                            {{ $card['hint'] }}
                        </p>
                    </a>
                @endforeach
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
                <h2 class="text-2xl font-black mb-6">
                    Accesos rápidos
                </h2>

                <div class="grid md:grid-cols-4 gap-5">
                    <a href="{{ route('admin.courts') }}"
                       class="p-6 rounded-2xl border border-gray-200 hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Canchas</h3>
                        <p class="text-sm text-gray-500 mt-2">Gestionar canchas.</p>
                    </a>

                    <a href="{{ route('admin.sports') }}"
                       class="p-6 rounded-2xl border border-gray-200 hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Deportes</h3>
                        <p class="text-sm text-gray-500 mt-2">Gestionar deportes.</p>
                    </a>

                    <a href="{{ route('admin.turns.index') }}"
                       class="p-6 rounded-2xl border border-gray-200 hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Turnos</h3>
                        <p class="text-sm text-gray-500 mt-2">Administrar turnos.</p>
                    </a>

                    <a href="{{ route('admin.club') }}"
                       class="p-6 rounded-2xl border border-gray-200 hover:border-lime-400 hover:bg-lime-50 transition">
                        <h3 class="font-black text-gray-900">Club</h3>
                        <p class="text-sm text-gray-500 mt-2">Configurar datos.</p>
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection
