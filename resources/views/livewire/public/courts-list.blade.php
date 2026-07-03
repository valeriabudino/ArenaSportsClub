<?php

use App\Models\Court;
use App\Models\Sport;
use Livewire\Volt\Component;

new class extends Component {
    public $selectedSport = '';
    public $selectedDate = '';

    public function with(): array
    {
        return [
            'sports' => Sport::all(),
            'courts' => Court::with('sport')
                ->where('is_active', true)
                ->when($this->selectedSport, fn($q) => $q->where('sport_id', $this->selectedSport))
                ->when($this->selectedDate, fn($q) => $q->whereHas('turns', fn($t) => $t->where('date', $this->selectedDate)->where('status', 'available')))
                ->get(),
        ];
    }
};

?>

<div>
    <!-- Filtro por deporte -->
    <div class="flex justify-center gap-4 mb-12">
        <button wire:click="$set('selectedSport', '')" class="px-6 py-2 rounded-full border border-lime-400 text-lime-400 hover:bg-lime-400 hover:text-[#07110d] transition {{ $selectedSport === '' ? 'bg-lime-400 text-[#07110d]' : '' }}">
            Todas
        </button>
        @foreach ($sports as $sport)
            <button wire:click="$set('selectedSport', {{ $sport->id }})" class="px-6 py-2 rounded-full border border-lime-400 text-lime-400 hover:bg-lime-400 hover:text-[#07110d] transition {{ $selectedSport == $sport->id ? 'bg-lime-400 text-[#07110d]' : '' }}">
                {{ $sport->name }}
            </button>
        @endforeach
    </div>

    <!-- Filtro por fecha -->
    <div class="flex justify-center mb-12">
        <input type="date" wire:model.live="selectedDate" class="px-4 py-2 rounded bg-white/10 text-white border border-lime-400">
    </div>

    <!-- Grilla de canchas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($courts as $court)
            <div class="bg-white/10 rounded-lg p-6">
                <h3 class="text-xl font-bold text-white">{{ $court->name }}</h3>
                <p class="text-white/70 text-sm">{{ $court->sport->name }}</p>
                <p class="text-white/70 mt-2">{{ $court->description }}</p>
                <p class="text-lime-400 font-bold mt-4">${{ number_format($court->price_per_hour, 0, ',', '.') }}/h</p>
            </div>
        @empty
            <p class="text-white/70 col-span-full text-center">No hay canchas disponibles</p>
        @endforelse
    </div>
</div>