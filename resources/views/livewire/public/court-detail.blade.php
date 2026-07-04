<?php

use App\Models\Court;
use App\Models\Turn;
use Illuminate\Support\Str;
use Livewire\Volt\Component;

new class extends Component {
    public Court $court;
    public $selectedDate = '';

    public function mount(Court $court)
    {
        $this->court = $court->load('sport');
        $this->selectedDate = now()->toDateString();
    }

    public function reserve($turnId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $turn = Turn::where('id', $turnId)->where('status', 'available')->first();

        if (!$turn) {
            session()->flash('error', 'El turno ya no está disponible');
            return;
        }

        $turn->update([
            'user_id' => auth()->id(),
            'status' => 'booked',
            'qr_code' => Str::uuid(),
        ]);

        session()->flash('success', 'Turno reservado con exito');
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
    @if (session('success'))
        <div class="bg-green-500 text-white px-4 py-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-500 text-white px-4 py-2 rounded mb-4">{{ session('error') }}</div>
    @endif

    <a href="/canchas" class="text-lime-400 hover:underline mb-8 inline-block">Volver a canchas</a>

    <h1 class="text-5xl font-black uppercase text-lime-400">{{ $court->name }}</h1>
    <p class="text-white/70 text-lg mt-2">{{ $court->sport->name }}</p>

    <div class="mt-8 bg-white/10 rounded-lg p-8">
        <p class="text-white/80">{{ $court->description }}</p>
        <p class="mt-4">Capacidad: {{ $court->capacity }} personas</p>
        <p class="mt-2">Precio: <span class="text-lime-400 font-bold text-2xl">${{ number_format($court->price_per_hour, 0, ',', '.') }}/h</span></p>
    </div>

    <div class="mt-12">
        <h2 class="text-2xl font-bold text-white mb-4">Disponibilidad</h2>
        <input type="date" wire:model.live="selectedDate" class="px-4 py-2 rounded bg-white/10 text-white border border-lime-400">
    </div>

    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @forelse ($turns as $turn)
            @php
                $isAvailable = $turn->status === 'available';
                $isBooked = $turn->status === 'booked';
            @endphp
            <div class="bg-white/10 rounded-lg p-4 text-center @if ($isAvailable) border border-lime-400 @else opacity-50 @endif">
                <p class="text-white font-bold">{{ substr($turn->start_time, 0, 5) }} - {{ substr($turn->end_time, 0, 5) }}</p>
                <p class="text-lime-400 font-bold mt-2">${{ number_format($turn->price, 0, ',', '.') }}</p>
                @if ($isAvailable)
                    <span class="text-green-400 text-sm">Disponible</span>
                    @auth
                        <button wire:click="reserve({{ $turn->id }})" class="mt-2 w-full px-3 py-1 bg-lime-400 text-gray-900 rounded font-bold text-sm hover:bg-lime-300 transition">Reservar</button>
                    @endauth
                @elseif ($isBooked)
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