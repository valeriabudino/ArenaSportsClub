<?php

use App\Models\Court;
use App\Models\Sport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public $sport_id = '';
    public $name = '';
    public $description = '';
    public $price_per_hour = '';
    public $capacity = '';
    public $is_active = true;
    public $editingId = null;
    public $showForm = false;

    public function save()
    {
        $this->validate([
            'sport_id' => 'required|exists:sports,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($this->editingId) {
            Court::findOrFail($this->editingId)->update([
                'sport_id' => $this->sport_id,
                'name' => $this->name,
                'description' => $this->description,
                'price_per_hour' => $this->price_per_hour,
                'capacity' => $this->capacity ?: null,
                'is_active' => $this->is_active,
            ]);
        } else {
            Court::create([
                'sport_id' => $this->sport_id,
                'name' => $this->name,
                'description' => $this->description,
                'price_per_hour' => $this->price_per_hour,
                'capacity' => $this->capacity ?: null,
                'is_active' => $this->is_active,
            ]);
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $court = Court::with('sport')->findOrFail($id);
        $this->editingId = $court->id;
        $this->sport_id = $court->sport_id;
        $this->name = $court->name;
        $this->description = $court->description;
        $this->price_per_hour = $court->price_per_hour;
        $this->capacity = $court->capacity;
        $this->is_active = $court->is_active;
        $this->showForm = true;
    }

    public function delete($id)
    {
        Court::findOrFail($id)->delete();
    }

    public function resetForm()
    {
        $this->sport_id = '';
        $this->name = '';
        $this->description = '';
        $this->price_per_hour = '';
        $this->capacity = '';
        $this->is_active = true;
        $this->editingId = null;
        $this->showForm = false;
    }

    public $courts = [];
    public $sports = [];

    public function mount(): void
    {
        $this->courts = Court::with('sport')->orderBy('name')->get();
        $this->sports = Sport::orderBy('name')->get();
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Canchas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Canchas</h3>
                        <button wire:click="$toggle('showForm')"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            {{ $showForm ? 'Cancelar' : 'Nueva cancha' }}
                        </button>
                    </div>

                    @if ($showForm)
                        <form wire:submit="save" class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Deporte</label>
                                    <select wire:model="sport_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Seleccionar...</option>
                                        @foreach ($sports as $sport)
                                            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sport_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input wire:model="name" type="text"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Precio por hora ($)</label>
                                    <input wire:model="price_per_hour" type="number" step="0.01"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('price_per_hour') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Capacidad (jugadores)</label>
                                    <input wire:model="capacity" type="number"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea wire:model="description" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="inline-flex items-center">
                                        <input wire:model="is_active" type="checkbox" value="1"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Activa</span>
                                    </label>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deporte</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio/hora</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Capacidad</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($courts as $court)
                                    <tr>
                                        <td class="px-6 py-4">{{ $court->name }}</td>
                                        <td class="px-6 py-4">{{ $court->sport->name }}</td>
                                        <td class="px-6 py-4">${{ number_format($court->price_per_hour, 2) }}</td>
                                        <td class="px-6 py-4 text-center">{{ $court->capacity ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 py-1 rounded text-xs {{ $court->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $court->is_active ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <button wire:click="edit({{ $court->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900">Editar</button>
                                            <button wire:click="delete({{ $court->id }})"
                                                    wire:confirm="¿Eliminar esta cancha?"
                                                    class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No hay canchas registradas.
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