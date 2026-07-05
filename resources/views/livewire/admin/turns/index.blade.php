<?php

use App\Models\Turn;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';

    public function with(): array
    {
        return [
            'turns' => Turn::with('court.sport')->when($this->search, fn($q) => $q->whereHas('court', fn($q) => $q->where('name', 'like', "%{$this->search}%")))->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))->when($this->dateFilter, fn($q) => $q->where('date', $this->dateFilter))->orderBy('date')->orderBy('start_time')->paginate(20),
        ];
    }
};

?>

<div>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">
            Gestión de turnos
        </h1>

        <p class="text-gray-500 mt-2">
            Consultá, filtrá y administrá los turnos disponibles o reservados.
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow p-8">
        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <input type="text" wire:model.live="search" placeholder="Buscar por cancha..."
                class="w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">

            <select wire:model.live="statusFilter"
                class="w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
                <option value="">Todos los estados</option>
                <option value="available">Disponible</option>
                <option value="pending_payment">Pago pendiente</option>
                <option value="booked">Reservado</option>
                <option value="cancelled">Cancelado</option>
            </select>

            <input type="date" wire:model.live="dateFilter"
                class="w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="min-w-full">
                <thead class="bg-[#07110d] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Deporte</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Cancha</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Inicio</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Fin</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Precio</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase">Estado</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($turns as $turn)
                        <tr class="hover:bg-lime-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $turn->court->sport->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $turn->court->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $turn->date }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ substr($turn->start_time, 0, 5) }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ substr($turn->end_time, 0, 5) }}
                            </td>

                            <td class="px-6 py-4 font-bold text-gray-900">
                                ${{ number_format($turn->price, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusLabels = [
                                        'available' => ['Disponible', 'bg-lime-100 text-lime-700'],
                                        'pending_payment' => ['Pago pendiente', 'bg-yellow-100 text-yellow-700'],
                                        'booked' => ['Reservado', 'bg-blue-100 text-blue-700'],
                                    ];
                                    [$label, $classes] = $statusLabels[$turn->status] ?? ['Cancelado', 'bg-red-100 text-red-700'];
                                @endphp

                                <span class="px-3 py-1 rounded-full text-sm font-bold {{ $classes }}">
                                    {{ $label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No hay turnos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $turns->links() }}
        </div>
    </div>
</div>
