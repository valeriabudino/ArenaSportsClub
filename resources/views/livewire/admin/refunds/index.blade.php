<?php

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component {
    public function markRefunded($paymentId)
    {
        $payment = Payment::where('id', $paymentId)->where('status', 'refund_pending')->first();

        if ($payment) {
            $payment->update(['status' => 'refunded']);
            session()->flash('success', 'Reembolso marcado como realizado.');
        }
    }

    public function with(): array
    {
        return [
            'pending' => Payment::with('turn.court.sport', 'user')
                ->where('status', 'refund_pending')
                ->latest()
                ->get(),
        ];
    }
};

?>

<div>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">
            Reembolsos pendientes
        </h1>

        <p class="text-gray-500 mt-2">
            Reservas canceladas con más de 1 día de anticipación: el club debe reembolsar el monto pagado por fuera del sistema y marcarlo acá una vez hecho.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-lime-100 text-lime-700 px-5 py-4 rounded-xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow p-8">
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="min-w-full">
                <thead class="bg-[#07110d] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Usuario</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Cancha</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Turno</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Monto</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($pending as $payment)
                        <tr class="hover:bg-lime-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $payment->user->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $payment->turn->court->name }} ({{ $payment->turn->court->sport->name }})
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ \Carbon\Carbon::parse($payment->turn->date)->format('d/m/Y') }}
                                {{ substr($payment->turn->start_time, 0, 5) }}
                            </td>

                            <td class="px-6 py-4 font-bold text-gray-900">
                                ${{ number_format($payment->amount, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <button wire:click="markRefunded({{ $payment->id }})"
                                        wire:confirm="¿Confirmás que ya reembolsaste este pago?"
                                        class="bg-lime-400 text-black px-4 py-2 rounded-xl font-black text-sm hover:bg-lime-300 transition">
                                    Marcar reembolsado
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No hay reembolsos pendientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
