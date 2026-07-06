<?php

use App\Models\Turn;
use App\Services\MercadoPagoService;
use Livewire\Volt\Component;

new class extends Component {
    public function pay($turnId)
    {
        $turn = Turn::where('id', $turnId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending_payment')
            ->first();

        if (!$turn) {
            return;
        }

        try {
            $result = app(MercadoPagoService::class)->createPreference($turn);

            return redirect()->away($result['checkout_url']);
        } catch (\Throwable $e) {
            session()->flash('error', 'No se pudo iniciar el pago con Mercado Pago. Intentá nuevamente.');
            report($e);
        }
    }

    public function cancel($turnId)
    {
        $turn = Turn::where('id', $turnId)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['booked', 'pending_payment'])
            ->first();

        if (!$turn) {
            return;
        }

        $approvedPayment = $turn->status === 'booked'
            ? $turn->payments()->where('status', 'approved')->latest()->first()
            : null;

        $turn->update([
            'status' => 'available',
            'user_id' => null,
        ]);

        if (!$approvedPayment) {
            session()->flash('success', 'Reserva cancelada correctamente.');
            return;
        }

        $turnStart = \Carbon\Carbon::parse("{$turn->date} {$turn->start_time}");
        $hoursUntilStart = now()->diffInHours($turnStart, false);

        if ($hoursUntilStart >= 24) {
            $approvedPayment->update(['status' => 'refund_pending']);
            session()->flash('success', 'Reserva cancelada. Como fue con más de 1 día de anticipación, el club te reembolsará el monto pagado.');
        } else {
            $approvedPayment->update(['status' => 'cancelled_no_refund']);
            session()->flash('success', 'Reserva cancelada. Como fue con menos de 1 día de anticipación, no corresponde reembolso.');
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
    <div class="mb-10">
        <span class="text-lime-400 font-black uppercase tracking-widest">
            Mi cuenta
        </span>

        <h1 class="text-4xl md:text-5xl font-black uppercase mt-3">
            Mis reservas
        </h1>

        <p class="text-white/60 mt-4">
            Consultá tus turnos reservados y cancelá los que ya no vayas a utilizar.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-lime-400 text-black px-5 py-4 rounded-xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-500 text-white px-5 py-4 rounded-xl font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6">
        @forelse ($reservations as $r)
            <article class="bg-white rounded-3xl overflow-hidden shadow-2xl grid md:grid-cols-[260px_1fr] text-[#07110d]">
                <div>
                    @if ($r->court->image)
                        <img src="{{ asset('storage/' . $r->court->image) }}"
                             alt="{{ $r->court->name }}"
                             class="h-full min-h-[220px] w-full object-cover">
                    @else
                        <div class="h-full min-h-[220px] bg-gray-200 flex items-center justify-center text-gray-400 font-bold">
                            Sin imagen
                        </div>
                    @endif
                </div>

                <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <span class="inline-block bg-lime-100 text-lime-700 px-3 py-1 rounded-full text-sm font-black">
                            {{ $r->court->sport->name }}
                        </span>

                        <h2 class="text-2xl font-black mt-3">
                            {{ $r->court->name }}
                        </h2>

                        <div class="grid sm:grid-cols-3 gap-4 mt-5 text-gray-600">
                            <div>
                                <p class="text-sm font-bold text-gray-400">Fecha</p>
                                <p class="font-black">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</p>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-gray-400">Horario</p>
                                <p class="font-black">
                                    {{ substr($r->start_time, 0, 5) }} - {{ substr($r->end_time, 0, 5) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-gray-400">Precio</p>
                                <p class="font-black text-lime-500">
                                    ${{ number_format($r->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="md:text-right">
                        @if ($r->status === 'booked' || $r->status === 'pending_payment')
                            <span class="inline-block px-4 py-2 rounded-full font-black text-sm
                                {{ $r->status === 'booked' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $r->status === 'booked' ? 'Reservado' : 'Pago pendiente' }}
                            </span>

                            @if ($r->status === 'booked' && $r->qr_code)
                                @php
                                    $qrStart = \Carbon\Carbon::parse("{$r->date} {$r->start_time}")->subMinutes(15);
                                    $qrEnd = \Carbon\Carbon::parse("{$r->date} {$r->end_time}");
                                    $qrState = now()->lessThan($qrStart)
                                        ? 'no_habilitado'
                                        : (now()->greaterThan($qrEnd) ? 'expirado' : 'activo');
                                    $qrStateLabels = [
                                        'no_habilitado' => 'Se habilita 15 min antes del turno',
                                        'activo' => 'Mostrá este código en el ingreso',
                                        'expirado' => 'El código ya expiró',
                                    ];
                                @endphp

                                <div class="mt-4 flex flex-col items-center md:items-end">
                                    <img src="{{ route('reservations.qr', $r) }}" alt="Código QR de acceso"
                                        class="w-32 h-32 {{ $qrState === 'activo' ? '' : 'opacity-40 grayscale' }}">

                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $qrStateLabels[$qrState] }}
                                    </p>
                                </div>
                            @endif

                            @if ($r->status === 'pending_payment')
                                <button wire:click="pay({{ $r->id }})"
                                        class="mt-4 block w-full md:w-auto bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                                    Pagar
                                </button>
                            @endif

                            <button wire:click="cancel({{ $r->id }})"
                                    wire:confirm="¿Cancelar esta reserva? Si faltan menos de 24hs para el turno no corresponde reembolso."
                                    class="mt-4 block w-full md:w-auto bg-red-500 text-white px-6 py-3 rounded-xl font-black hover:bg-red-600 transition">
                                Cancelar
                            </button>
                        @else
                            <span class="inline-block bg-red-100 text-red-700 px-4 py-2 rounded-full font-black text-sm">
                                Cancelado
                            </span>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white/10 rounded-3xl p-10 text-center border border-white/10">
                <h2 class="text-2xl font-black text-white">
                    No tenés reservas todavía
                </h2>

                <p class="text-white/60 mt-3">
                    Elegí una cancha y reservá tu primer turno.
                </p>

                <a href="{{ route('courts.index') }}"
                   class="inline-block mt-6 bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                    Ver canchas
                </a>
            </div>
        @endforelse
    </div>
</div>