<?php

use App\Models\Court;
use App\Models\CourtReview;
use App\Models\Turn;
use App\Services\MercadoPagoService;
use Livewire\Volt\Component;

new class extends Component {
    public Court $court;
    public $selectedDate = '';
    public $selectedTurnId = null;
    public $rating = 5;
    public $comment = '';

    public function mount(Court $court)
    {
        $this->court = $court->load('sport');
        $this->selectedDate = now()->toDateString();
    }

    public function selectTurn($turnId)
    {
        $this->selectedTurnId = $turnId;
    }

    public function reserve($turnId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $turn = Turn::where('id', $turnId)->where('status', 'available')->first();

        if (!$turn) {
            $this->dispatch('close-modal', 'confirmar-reserva');
            session()->flash('error', 'El turno ya no está disponible');
            return;
        }

        $turn->update([
            'user_id' => auth()->id(),
            'status' => 'pending_payment',
        ]);

        try {
            $result = app(MercadoPagoService::class)->createPreference($turn);

            return redirect()->away($result['checkout_url']);
        } catch (\Throwable $e) {
            $turn->update(['user_id' => null, 'status' => 'available']);
            $this->dispatch('close-modal', 'confirmar-reserva');
            session()->flash('error', 'No se pudo iniciar el pago con Mercado Pago. Intentá nuevamente.');
            report($e);
        }
    }

    public function saveReview(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: true);
            return;
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        CourtReview::create([
            'court_id' => $this->court->id,
            'user_id' => auth()->id(),
            'rating' => $this->rating,
            'comment' => $this->comment,
        ]);

        $this->rating = 5;
        $this->comment = '';

        session()->flash('success', 'Comentario publicado correctamente.');
    }

    public function with(): array
    {
        return [
            'turns' => Turn::where('court_id', $this->court->id)->where('date', $this->selectedDate)->orderBy('start_time')->get(),
            'selectedTurn' => $this->selectedTurnId ? Turn::with('court')->find($this->selectedTurnId) : null,

            'reviews' => $this->court->reviews()->with('user')->latest()->get(),
            'averageRating' => round($this->court->reviews()->avg('rating'), 1),
        ];
    }
};

?>

<div class="max-w-7xl mx-auto px-6 lg:px-8">

    @if (session('success'))
        <div class="bg-lime-400 text-black px-5 py-4 rounded-xl mb-6 font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-500 text-white px-5 py-4 rounded-xl mb-6 font-bold">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('courts.index') }}"
        class="inline-flex items-center text-lime-400 hover:text-lime-300 font-bold mb-8">
        ← Volver a canchas
    </a>

    <div class="grid lg:grid-cols-2 gap-10 items-start">

        {{-- Imagen --}}
        <div class="bg-white/10 rounded-3xl overflow-hidden shadow-2xl">
            @if ($court->image)
                <img src="{{ asset('storage/' . $court->image) }}" alt="{{ $court->name }}"
                    class="w-full h-[480px] object-cover">
            @else
                <div class="w-full h-[480px] bg-white/10 flex items-center justify-center text-white/50 font-bold">
                    Sin imagen
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="bg-white rounded-3xl p-8 shadow-2xl text-[#07110d]">
            <span class="inline-block bg-lime-100 text-lime-700 px-4 py-2 rounded-full font-black text-sm">
                {{ $court->sport->name }}
            </span>

            <h1 class="text-4xl md:text-5xl font-black uppercase mt-5">
                {{ $court->name }}
            </h1>

            <p class="text-gray-600 mt-5">
                {{ $court->description ?: 'Cancha disponible para reservar.' }}
            </p>

            <div class="grid grid-cols-2 gap-4 mt-8">
                <div class="bg-gray-100 rounded-2xl p-5">
                    <p class="text-gray-500 text-sm font-bold">Capacidad</p>
                    <p class="text-xl font-black mt-1">
                        {{ $court->capacity ?? '-' }} jugadores
                    </p>
                </div>

                <div class="bg-gray-100 rounded-2xl p-5">
                    <p class="text-gray-500 text-sm font-bold">Precio por hora</p>
                    <p class="text-2xl font-black text-lime-500 mt-1">
                        ${{ number_format($court->price_per_hour, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="mt-8">
                <label class="font-black text-sm uppercase">
                    Seleccionar fecha
                </label>

                <input type="date" wire:model.live="selectedDate"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>
        </div>
    </div>

    {{-- Disponibilidad --}}
    <div class="mt-14">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-black text-white">
                    Horarios disponibles
                </h2>

                <p class="text-white/60 mt-1">
                    Elegí un turno para confirmar tu reserva.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse ($turns as $turn)
                @php
                    $isAvailable = $turn->status === 'available';
                    $isPendingPayment = $turn->status === 'pending_payment';
                    $isBooked = $turn->status === 'booked';
                @endphp

                <div
                    class="rounded-2xl p-5 border
                    {{ $isAvailable ? 'bg-white text-[#07110d] border-lime-400' : 'bg-white/10 text-white border-white/10 opacity-60' }}">

                    <p class="font-black text-lg">
                        {{ substr($turn->start_time, 0, 5) }} - {{ substr($turn->end_time, 0, 5) }}
                    </p>

                    <p class="font-black text-lime-500 mt-2">
                        ${{ number_format($turn->price, 0, ',', '.') }}
                    </p>

                    <div class="mt-4">
                        @if ($isAvailable)
                            <span
                                class="inline-block bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-bold">
                                Disponible
                            </span>

                            @auth
                                <button type="button"
                                    x-data=""
                                    x-on:click="$dispatch('open-modal', 'confirmar-reserva')"
                                    wire:click="selectTurn({{ $turn->id }})"
                                    class="mt-4 w-full bg-lime-400 text-black rounded-xl py-3 font-black hover:bg-lime-300 transition">
                                    Reservar
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                    class="mt-4 block text-center w-full bg-lime-400 text-black rounded-xl py-3 font-black hover:bg-lime-300 transition">
                                    Iniciar sesión
                                </a>
                            @endauth
                        @elseif ($isPendingPayment)
                            <span
                                class="inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-bold">
                                Pago pendiente
                            </span>
                        @elseif ($isBooked)
                            <span
                                class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">
                                Reservado
                            </span>
                        @else
                            <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                                Cancelado
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white/10 rounded-3xl p-10 text-center border border-white/10">
                    <h3 class="text-2xl font-black text-white">
                        No hay turnos disponibles
                    </h3>

                    <p class="text-white/60 mt-3">
                        Probá seleccionando otra fecha.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Confirmacion de reserva --}}
    <x-modal name="confirmar-reserva" focusable>
        @if ($selectedTurn)
            <div class="p-8">
                <h2 class="text-2xl font-black text-[#07110d]">
                    Confirmar reserva
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    Revisá los datos antes de pagar con Mercado Pago.
                </p>

                <div class="mt-6 bg-gray-100 rounded-2xl p-5 space-y-3 text-[#07110d]">
                    <div class="flex justify-between">
                        <span class="font-bold text-gray-500">Cancha</span>
                        <span class="font-black">{{ $selectedTurn->court->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-bold text-gray-500">Fecha</span>
                        <span class="font-black">{{ \Carbon\Carbon::parse($selectedTurn->date)->format('d/m/Y') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="font-bold text-gray-500">Horario</span>
                        <span class="font-black">
                            {{ substr($selectedTurn->start_time, 0, 5) }} - {{ substr($selectedTurn->end_time, 0, 5) }}
                        </span>
                    </div>

                    <div class="flex justify-between text-lg pt-2 border-t border-gray-200">
                        <span class="font-bold text-gray-500">Total a pagar</span>
                        <span class="font-black text-lime-500">${{ number_format($selectedTurn->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')"
                        class="px-6 py-3 rounded-xl font-black border border-gray-300 hover:bg-gray-100 transition">
                        Cancelar
                    </button>

                    <button wire:click="reserve({{ $selectedTurn->id }})"
                        class="bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                        Pagar con Mercado Pago
                    </button>
                </div>
            </div>
        @endif
    </x-modal>

    {{-- Comentarios --}}
    <div class="mt-16 bg-white rounded-3xl p-8 shadow-2xl text-[#07110d]">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-black">
                    Comentarios y valoraciones
                </h2>

                <p class="text-gray-500 mt-2">
                    Opiniones de usuarios sobre esta cancha.
                </p>
            </div>

            <div class="flex items-center gap-2 text-lime-500 font-black text-2xl">
                <svg class="h-7 w-7 fill-current" viewBox="0 0 24 24">
                    <path d="M12 3l2.7 5.5 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.8 1-6.1-4.4-4.3 6.1-.9L12 3z" />
                </svg>

                {{ $averageRating ?: 'Sin valorar' }}
            </div>
        </div>

        @auth
            <form wire:submit="saveReview" class="bg-gray-100 rounded-2xl p-6 mb-8">
                <div class="grid md:grid-cols-[180px_1fr] gap-5">
                    <div>
                        <label class="font-bold text-sm">Puntuación</label>

                        <select wire:model="rating"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
                            <option value="5">5 estrellas</option>
                            <option value="4">4 estrellas</option>
                            <option value="3">3 estrellas</option>
                            <option value="2">2 estrellas</option>
                            <option value="1">1 estrella</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-sm">Comentario</label>

                        <textarea wire:model="comment" rows="3" placeholder="Contá tu experiencia..."
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"></textarea>
                    </div>
                </div>

                <div class="flex justify-end mt-5">
                    <button type="submit"
                        class="bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                        Publicar comentario
                    </button>
                </div>
            </form>
        @else
            <div class="bg-gray-100 rounded-2xl p-6 mb-8 text-center">
                <p class="font-bold text-gray-600">
                    Iniciá sesión para dejar tu valoración.
                </p>

                <a href="{{ route('login') }}"
                    class="inline-block mt-4 bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                    Iniciar sesión
                </a>
            </div>
        @endauth

        <div class="space-y-5">
            @forelse ($reviews as $review)
                <div class="border border-gray-100 rounded-2xl p-5">
                    <div class="flex justify-between gap-4">
                        <div>
                            <p class="font-black">
                                {{ $review->user->name }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ $review->created_at->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="text-lime-500 font-black">
                            {{ str_repeat('★', $review->rating) }}
                        </div>
                    </div>

                    <p class="text-gray-600 mt-4">
                        {{ $review->comment ?: 'Sin comentario.' }}
                    </p>
                </div>
            @empty
                <p class="text-gray-500 text-center py-6">
                    Todavía no hay comentarios para esta cancha.
                </p>
            @endforelse
        </div>
    </div>
</div>
