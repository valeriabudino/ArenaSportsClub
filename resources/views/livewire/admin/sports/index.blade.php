<?php

use App\Models\Sport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
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
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Deportes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Deportes</h3>
                        <button wire:click="$toggle('showForm')"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            {{ $showForm ? 'Cancelar' : 'Nuevo deporte' }}
                        </button>
                    </div>

                    @if ($showForm)
                        <form wire:submit="save" class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input wire:model="name" type="text"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Icono</label>
                                    <input wire:model="icon" type="text" placeholder="ej: futbol, tenis..."
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    {{ $editingId ? 'Actualizar' : 'Guardar' }}
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icono</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Canchas</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($sports as $sport)
                                    <tr>
                                        <td class="px-6 py-4">{{ $sport->name }}</td>
                                        <td class="px-6 py-4">{{ $sport->icon ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">{{ $sport->courts_count }}</td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button wire:click="edit({{ $sport->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                            <button wire:click="delete({{ $sport->id }})"
                                                    wire:confirm="¿Eliminar este deporte?"
                                                    class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            No hay deportes registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>