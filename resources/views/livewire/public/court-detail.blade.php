<?php

use App\Models\Court;
use Livewire\Volt\Component;

new class extends Component {
    public Court $court;

    public function mount(Court $court)
    {
        $this->court = $court->load('sport');
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
</div>