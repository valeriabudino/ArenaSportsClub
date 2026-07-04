<?php

use App\Models\Sport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public $name = '';
    public $icon = '';
    public $editingId = null;
    public $showForm = false;

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
        ]);

        if ($this->editingId) {
            Sport::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'icon' => $this->icon,
            ]);
        } else {
            Sport::create([
                'name' => $this->name,
                'icon' => $this->icon,
            ]);
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $sport = Sport::findOrFail($id);
        $this->editingId = $sport->id;
        $this->name = $sport->name;
        $this->icon = $sport->icon;
        $this->showForm = true;
    }

    public function delete($id)
    {
        $sport = Sport::findOrFail($id);

        if ($sport->courts()->count() > 0) {
            session()->flash('error', 'No se puede eliminar un deporte con canchas asociadas.');
            return;
        }

        $sport->delete();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->icon = '';
        $this->editingId = null;
        $this->showForm = false;
    }

    public $sports = [];

    public function mount(): void
    {
        $this->sports = Sport::withCount('courts')->orderBy('name')->get();
    }
}; ?>

<div>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">
            Gestión de deportes
        </h1>
        <p class="text-gray-500 mt-2">
            Administrá los deportes disponibles dentro del complejo.
        </p>
    </div>

    @if (session('error'))
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-black text-gray-900">
                Deportes registrados
            </h2>

            <button
                wire:click="$toggle('showForm')"
                class="bg-lime-400 text-black px-5 py-3 rounded-xl font-black hover:bg-lime-300 transition"
            >
                {{ $showForm ? 'Cancelar' : 'Nuevo deporte' }}
            </button>
        </div>

        @if ($showForm)
            <form wire:submit="save" class="mb-8 rounded-2xl bg-gray-50 p-6 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">
                            Nombre
                        </label>

                        <input
                            wire:model="name"
                            type="text"
                            class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-lime-400 focus:ring-lime-400"
                        >

                        @error('name')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700">
                            Icono
                        </label>

                        <input
                            wire:model="icon"
                            type="text"
                            placeholder="ej: futbol, padel, tenis..."
                            class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-lime-400 focus:ring-lime-400"
                        >

                        @error('icon')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="submit"
                        class="bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition"
                    >
                        {{ $editingId ? 'Actualizar deporte' : 'Guardar deporte' }}
                    </button>
                </div>
            </form>
        @endif

        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#07110d]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-black text-white uppercase">Nombre</th>
                        <th class="px-6 py-4 text-left text-xs font-black text-white uppercase">Icono</th>
                        <th class="px-6 py-4 text-center text-xs font-black text-white uppercase">Canchas</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-white uppercase">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($sports as $sport)
                        <tr class="hover:bg-lime-50/50 transition">
                            <td class="px-6 py-4 font-bold text-gray-900">
                                {{ $sport->name }}
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $sport->icon ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-lime-100 px-3 py-1 text-sm font-black text-lime-700">
                                    {{ $sport->courts_count }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right space-x-3">
                                <button
                                    wire:click="edit({{ $sport->id }})"
                                    class="font-bold text-lime-600 hover:text-lime-700"
                                >
                                    Editar
                                </button>

                                <button
                                    wire:click="delete({{ $sport->id }})"
                                    wire:confirm="¿Eliminar este deporte?"
                                    class="font-bold text-red-600 hover:text-red-700"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No hay deportes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>