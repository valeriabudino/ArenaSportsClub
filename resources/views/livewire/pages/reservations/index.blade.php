<?php

use App\Models\Turn;
use Livewire\Volt\Component;

new class extends Component {
    public function cancel($turnId)
    {
        $turn = Turn::where('id', $turnId)->where('user_id', auth()->id())->where('status', 'booked')->first();

        if ($turn) {
            $turn->update(['status' => 'cancelled', 'user_id' => null]);
            session()->flash('success', 'Reserva cancelada');
        }
    }

    public function with(): array
    {
        return [
            'reservations' => Turn::with('court.sport')
                ->where('user_id', auth()->id())
                ->orderBy('date', 'desc')
                ->orderBy('start_time', 'desc')
                ->get(),
        ];
    }
};

?>

<div>
    <h1 class="text-2xl font-bold mb-4">Mis Reservas</h1>

    @if (session('success'))
        <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">Cancha</th>
                <th class="border px-4 py-2">Deporte</th>
                <th class="border px-4 py-2">Fecha</th>
                <th class="border px-4 py-2">Horario</th>
                <th class="border px-4 py-2">Precio</th>
                <th class="border px-4 py-2">Estado</th>
                <th class="border px-4 py-2">Accion</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $r)
                <tr>
                    <td class="border px-4 py-2">{{ $r->court->name }}</td>
                    <td class="border px-4 py-2">{{ $r->court->sport->name }}</td>
                    <td class="border px-4 py-2">{{ $r->date }}</td>
                    <td class="border px-4 py-2">{{ substr($r->start_time, 0, 5) }} - {{ substr($r->end_time, 0, 5) }}</td>
                    <td class="border px-4 py-2">${{ number_format($r->price, 0, ',', '.') }}</td>
                    <td class="border px-4 py-2">
                        <span class="px-2 py-1 rounded text-white {{ $r->status === 'booked' ? 'bg-blue-500' : 'bg-red-500' }}">
                            {{ ucfirst($r->status) }}
                        </span>
                    </td>
                    <td class="border px-4 py-2">
                        @if ($r->status === 'booked')
                            <button wire:click="cancel({{ $r->id }})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Cancelar</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border px-4 py-2 text-center">No tenes reservas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>