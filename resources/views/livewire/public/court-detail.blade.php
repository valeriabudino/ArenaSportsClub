<?php

use App\Models\Court;
use App\Models\Turn;
use Livewire\Volt\Component;

new class extends Component {
    public Court $court;
    public $selectedDate = '';

    public function mount(Court $court)
    {
        $this->court = $court->load('sport');
        $this->selectedDate = now()->toDateString();
    }

    public function with(): array
    {
        return [
            'turns' => Turn::where('court_id', $this->court->id)
                ->where('date', $this->selectedDate)
                ->orderBy('start_time')
                ->get(),
        ];
    }
};

?>

<div class="max-w-4xl mx-auto">
    <a href="/canchas" class="text-lime-400 hover:underline mb-8 inline-block">&larr; Volver a canchas</a>

    <h1 class="text-5xl font-black uppercase text-lime-400">{{ $court->name }}</h1>
    <p class="text-white/70 text-lg mt-2">{{ $court->sport->name }}</p>

    <div class="mt-8 bg-white/10 rounded-lg p-8">
        <p class="text-white/80">{{ $court->description }}</p>
        <p class="mt-4"><span class="text-white/70">Capacidad:</span> {{ $court->capacity }} personas</p>
        <p class="mt-2"><span class="text-white/70">Precio:</span> <span class="text-lime-400 font-bold text-2xl">${{ number_format($court->price_per_hour, 0, ',', '.') }}/h</span></p>
    </div>

    <!-- Selector de fecha -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-white mb-4">Disponibilidad</h2>
        <input type="date" wire:model.live="selectedDate" class="px-4 py-2 rounded bg-white/10 text-white border border-lime-400">
    </div>

    <!-- Grilla de turnos -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse ($turns as $turn)
            <div class="bg-white/10 rounded-lg p-4 text-center {{ $turn->status === 'available' ? 'border border-lime-400' : 'opacity-50' }}">
                <p class="text-white font-bold">{{ substr($turn->start_time, 0, 5) }} - {{ substr($turn->end_time, 0, 5) }}</p>
                <p class="text-lime-400 font-bold mt-2">${{ number_format($turn->price, 0, ',', '.') }}</p>
                @if ($turn->status === 'available')
                    <span class="text-green-400 text-sm">Disponible</span>
                @elseif ($turn->status === 'booked')
                    <span class="text-blue-400 text-sm">Reservado</span>
                @else
                    <span class="text-red-400 text-sm">Cancelado</span>
                @endif
            </div>
        @empty
            <p class="text-white/70 col-span-full">No hay turnos disponibles para esta fecha</p>
        @endforelse
    </div>
</div>