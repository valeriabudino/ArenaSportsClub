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
            'sports' => Sport::orderBy('name')->get(),
            'courts' => Court::with('sport')
                ->where('is_active', true)
                ->when($this->selectedSport, fn($q) => $q->where('sport_id', $this->selectedSport))
                ->when($this->selectedDate, fn($q) => $q->whereHas('turns', fn($t) => $t->where('date', $this->selectedDate)->where('status', 'available')))
                ->orderBy('name')
                ->get(),
        ];
    }
};

?>

<div>
    <div class="bg-white/10 backdrop-blur rounded-3xl p-6 mb-12 border border-white/10">
        <div class="grid md:grid-cols-[1fr_auto] gap-6 items-end">
            <div>
                <label class="block text-sm font-black uppercase text-lime-400 mb-3">
                    Filtrar por deporte
                </label>

                <div class="flex flex-wrap gap-3">
                    <button wire:click="$set('selectedSport', '')"
                        class="px-5 py-3 rounded-xl font-black border transition
                        {{ $selectedSport === '' ? 'bg-lime-400 text-black border-lime-400' : 'text-white border-white/20 hover:border-lime-400 hover:text-lime-400' }}">
                        Todas
                    </button>

                    @foreach ($sports as $sport)
                        <button wire:click="$set('selectedSport', {{ $sport->id }})"
                            class="px-5 py-3 rounded-xl font-black border transition
                            {{ $selectedSport == $sport->id ? 'bg-lime-400 text-black border-lime-400' : 'text-white border-white/20 hover:border-lime-400 hover:text-lime-400' }}">
                            {{ $sport->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-black uppercase text-lime-400 mb-3">
                    Fecha
                </label>

                <input type="date"
                    wire:model.live="selectedDate"
                    class="w-full rounded-xl border-white/20 bg-white text-[#07110d] font-bold focus:border-lime-400 focus:ring-lime-400">
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse ($courts as $court)
            <article class="bg-white rounded-3xl overflow-hidden shadow-2xl hover:-translate-y-2 transition duration-300">
                @if ($court->image)
                    <img src="{{ asset('storage/' . $court->image) }}"
                        alt="{{ $court->name }}"
                        class="h-60 w-full object-cover">
                @else
                    <div class="h-60 bg-[#101814] flex items-center justify-center text-white/50 font-bold">
                        Sin imagen
                    </div>
                @endif

                <div class="p-6 text-[#07110d]">
                    <div class="flex justify-between items-start gap-4">
                        <div>
                            <h3 class="text-2xl font-black">
                                {{ $court->name }}
                            </h3>

                            <p class="text-lime-600 font-black mt-1">
                                {{ $court->sport->name }}
                            </p>
                        </div>

                        <span class="bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-black">
                            Activa
                        </span>
                    </div>

                    <p class="text-gray-600 mt-4 line-clamp-2">
                        {{ $court->description ?: 'Cancha disponible para reservar.' }}
                    </p>

                    <div class="space-y-3 mt-5 text-gray-600">
                        <p class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-lime-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21a5 5 0 00-10 0M12 12a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                            {{ $court->capacity ? 'Hasta ' . $court->capacity . ' jugadores' : 'Capacidad a consultar' }}
                        </p>

                        <p class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-lime-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Disponible para reservar
                        </p>
                    </div>

                    <div class="flex justify-between items-center mt-8">
                        <span class="font-black text-xl">
                            ${{ number_format($court->price_per_hour, 0, ',', '.') }}/h
                        </span>

                        <a href="{{ route('courts.show', $court) }}"
                            class="bg-lime-400 text-black px-5 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                            Ver cancha
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white/10 rounded-3xl p-10 text-center border border-white/10">
                <h3 class="text-2xl font-black text-white">
                    No hay canchas disponibles
                </h3>

                <p class="text-white/60 mt-3">
                    Probá cambiando los filtros o consultá nuevamente más tarde.
                </p>
            </div>
        @endforelse
    </div>
</div>