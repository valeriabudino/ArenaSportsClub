<?php

use App\Models\Turn;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';

    public function with(): array
    {
        return [
            'turns' => Turn::with('court.sport')
                ->when($this->search, fn($q) => $q->whereHas('court', fn($q) => $q->where('name', 'like', "%{$this->search}%")))
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
                ->when($this->dateFilter, fn($q) => $q->where('date', $this->dateFilter))
                ->orderBy('date')
                ->orderBy('start_time')
                ->paginate(20),
        ];
    }
};

?>

<div>
    <h1 class="text-2xl font-bold mb-4">Turnos</h1>

    <div class="flex gap-4 mb-4">
        <input type="text" wire:model.live="search" placeholder="Buscar por cancha..." class="border rounded px-3 py-2">
        <select wire:model.live="statusFilter" class="border rounded px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="available">Disponible</option>
            <option value="booked">Reservado</option>
            <option value="cancelled">Cancelado</option>
        </select>
        <input type="date" wire:model.live="dateFilter" class="border rounded px-3 py-2">
    </div>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">Deporte</th>
                <th class="border px-4 py-2">Cancha</th>
                <th class="border px-4 py-2">Fecha</th>
                <th class="border px-4 py-2">Inicio</th>
                <th class="border px-4 py-2">Fin</th>
                <th class="border px-4 py-2">Precio</th>
                <th class="border px-4 py-2">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($turns as $turn)
                <tr>
                    <td class="border px-4 py-2">{{ $turn->court->sport->name }}</td>
                    <td class="border px-4 py-2">{{ $turn->court->name }}</td>
                    <td class="border px-4 py-2">{{ $turn->date }}</td>
                    <td class="border px-4 py-2">{{ substr($turn->start_time, 0, 5) }}</td>
                    <td class="border px-4 py-2">{{ substr($turn->end_time, 0, 5) }}</td>
                    <td class="border px-4 py-2">${{ number_format($turn->price, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2">
                        <span class="px-2 py-1 rounded text-white {{ $turn->status === 'available' ? 'bg-green-500' : ($turn->status === 'booked' ? 'bg-blue-500' : 'bg-red-500') }}">
                            {{ ucfirst($turn->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border px-4 py-2 text-center">No hay turnos</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $turns->links() }}
    </div>
</div>