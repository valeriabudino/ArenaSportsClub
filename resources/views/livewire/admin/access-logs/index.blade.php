<?php

use App\Models\AccessLog;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component {
    public string $date = '';

    public function mount(): void
    {
        $this->date = now()->toDateString();
    }

    public function sync(): void
    {
        Artisan::call('accesos:sincronizar', ['--date' => $this->date]);

        session()->flash('success', trim(Artisan::output()));
    }

    public function with(): array
    {
        $logs = AccessLog::whereDate('date', $this->date)
            ->orderByDesc('scanned_at')
            ->get();

        return [
            'logs' => $logs,
            'total' => $logs->count(),
            'approved' => $logs->where('status', 'approved')->count(),
            'rejected' => $logs->where('status', 'rejected')->count(),
        ];
    }
};

?>

<div>
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900">
                Reporte de accesos por QR
            </h1>

            <p class="text-gray-500 mt-2">
                Escaneos detectados por el lector físico del control de accesos, sincronizados desde su sistema.
            </p>
        </div>

        <div class="flex items-end gap-3">
            <div>
                <label class="text-xs font-black uppercase text-gray-500">Fecha</label>
                <input type="date" wire:model.live="date"
                    class="mt-1 rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <button wire:click="sync"
                    class="bg-lime-400 text-black px-5 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                Sincronizar ahora
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-lime-100 text-lime-700 px-5 py-4 rounded-xl font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm font-bold text-gray-400">Total escaneos</p>
            <p class="text-3xl font-black text-gray-900 mt-1">{{ $total }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm font-bold text-gray-400">Aprobados</p>
            <p class="text-3xl font-black text-lime-600 mt-1">{{ $approved }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-sm font-bold text-gray-400">Rechazados</p>
            <p class="text-3xl font-black text-red-500 mt-1">{{ $rejected }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-8">
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="min-w-full">
                <thead class="bg-[#07110d] text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Hora</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">QR</th>
                        <th class="px-6 py-4 text-center text-xs font-black uppercase">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-black uppercase">Detalle</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-lime-50 transition">
                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->scanned_at->format('H:i:s') }}
                            </td>

                            <td class="px-6 py-4 font-mono text-sm text-gray-700">
                                {{ $log->qr_code }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-bold
                                    {{ $log->status === 'approved' ? 'bg-lime-100 text-lime-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $log->status === 'approved' ? 'Aprobado' : 'Rechazado' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ $log->details }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No hay escaneos sincronizados para esta fecha.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
