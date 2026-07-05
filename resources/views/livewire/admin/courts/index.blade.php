<?php

use App\Models\Court;
use App\Models\Sport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.admin')] class extends Component {
    use WithFileUploads;
    public $sport_id = '';
    public $name = '';
    public $description = '';
    public $price_per_hour = '';
    public $capacity = '';
    public $image;
    public $currentImage = '';
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
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = $this->currentImage;

        if ($this->image) {
            $imagePath = $this->image->store('courts', 'public');
        }
        if ($this->editingId) {
            Court::findOrFail($this->editingId)->update([
                'sport_id' => $this->sport_id,
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath,
                'price_per_hour' => $this->price_per_hour,
                'capacity' => $this->capacity ?: null,
                'is_active' => $this->is_active,
            ]);
        } else {
            Court::create([
                'sport_id' => $this->sport_id,
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath,
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
        $this->currentImage = $court->image;
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
        $this->image = null;
        $this->currentImage = '';
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

    {{-- Header --}}
    <div class="mb-8">

        <h1 class="text-3xl font-black text-gray-900">
            Gestión de canchas
        </h1>

        <p class="text-gray-500 mt-2">
            Administrá las canchas disponibles, precios y características.
        </p>

    </div>



    {{-- Contenedor --}}
    <div class="bg-white rounded-2xl shadow p-8">


        <div class="flex justify-between items-center mb-6">

            <h2 class="text-xl font-black text-gray-900">
                Canchas registradas
            </h2>


            <button wire:click="$toggle('showForm')"
                class="bg-lime-400 text-black px-5 py-3 rounded-xl font-black hover:bg-lime-300 transition">

                {{ $showForm ? 'Cancelar' : 'Nueva cancha' }}

            </button>

        </div>



        {{-- Formulario --}}
        @if ($showForm)

            <form wire:submit="save" class="mb-8 bg-gray-50 rounded-2xl border border-gray-100 p-6">


                <div class="grid md:grid-cols-2 gap-5">


                    {{-- Deporte --}}
                    <div>

                        <label class="font-bold text-sm text-gray-700">
                            Deporte
                        </label>


                        <select wire:model="sport_id"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">


                            <option value="">
                                Seleccionar...
                            </option>


                            @foreach ($sports as $sport)
                                <option value="{{ $sport->id }}">
                                    {{ $sport->name }}
                                </option>
                            @endforeach


                        </select>


                        @error('sport_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror

                    </div>




                    {{-- Nombre --}}
                    <div>

                        <label class="font-bold text-sm text-gray-700">
                            Nombre
                        </label>


                        <input wire:model="name" type="text"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">


                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror

                    </div>




                    {{-- Precio --}}
                    <div>

                        <label class="font-bold text-sm text-gray-700">
                            Precio por hora
                        </label>


                        <input wire:model="price_per_hour" type="number"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">


                        @error('price_per_hour')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror

                    </div>





                    {{-- Capacidad --}}
                    <div>

                        <label class="font-bold text-sm text-gray-700">
                            Capacidad jugadores
                        </label>


                        <input wire:model="capacity" type="number"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">

                    </div>




                    {{-- Descripcion --}}
                    <div class="md:col-span-2">

                        <label class="font-bold text-sm text-gray-700">
                            Descripción
                        </label>


                        <textarea wire:model="description" rows="3"
                            class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"></textarea>

                    </div>

                    {{-- Imagen --}}
                    <div class="md:col-span-2">
                        <label class="font-bold text-sm text-gray-700">
                            Imagen de la cancha
                        </label>

                        <input wire:model="image" type="file" accept="image/*"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white p-3 focus:border-lime-400 focus:ring-lime-400">

                        @error('image')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror

                        @if ($image)
                            <div class="mt-4">
                                <p class="text-sm font-bold text-gray-500 mb-2">Vista previa:</p>
                                <img src="{{ $image->temporaryUrl() }}"
                                    class="h-32 w-56 object-cover rounded-xl border">
                            </div>
                        @elseif ($currentImage)
                            <div class="mt-4">
                                <p class="text-sm font-bold text-gray-500 mb-2">Imagen actual:</p>
                                <img src="{{ asset('storage/' . $currentImage) }}"
                                    class="h-32 w-56 object-cover rounded-xl border">
                            </div>
                        @endif
                    </div>


                    {{-- Activa --}}
                    <div class="md:col-span-2">

                        <label class="flex items-center gap-2">

                            <input wire:model="is_active" type="checkbox"
                                class="rounded text-lime-400 focus:ring-lime-400">

                            <span class="font-bold text-gray-700">
                                Cancha activa
                            </span>

                        </label>

                    </div>


                </div>




                <div class="mt-6 flex justify-end">

                    <button type="submit"
                        class="bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">

                        {{ $editingId ? 'Actualizar cancha' : 'Guardar cancha' }}

                    </button>

                </div>


            </form>

        @endif





        {{-- Tabla --}}
        <div class="overflow-x-auto rounded-2xl border border-gray-100">

            <table class="min-w-full">

                <thead class="bg-[#07110d] text-white">

                    <tr>
                        <th class="px-6 py-4 text-left uppercase text-xs">Imagen</th>
                        <th class="px-6 py-4 text-left uppercase text-xs">Nombre</th>
                        <th class="px-6 py-4 text-left uppercase text-xs">Deporte</th>
                        <th class="px-6 py-4 text-left uppercase text-xs">Precio</th>
                        <th class="px-6 py-4 text-center uppercase text-xs">Capacidad</th>
                        <th class="px-6 py-4 text-center uppercase text-xs">Estado</th>
                        <th class="px-6 py-4 text-right uppercase text-xs">Acciones</th>
                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">


                    @forelse ($courts as $court)
                        <tr class="hover:bg-lime-50 transition">

                            <td class="px-6 py-4">

                                @if ($court->image)
                                    <img src="{{ asset('storage/' . $court->image) }}"
                                        class="h-16 w-24 object-cover rounded-xl">
                                @else
                                    <span class="text-gray-400 text-sm">
                                        Sin imagen
                                    </span>
                                @endif

                            </td>

                            <td class="px-6 py-4 font-bold">
                                {{ $court->name }}
                            </td>


                            <td class="px-6 py-4">
                                {{ $court->sport->name }}
                            </td>


                            <td class="px-6 py-4">
                                ${{ number_format($court->price_per_hour, 2) }}
                            </td>


                            <td class="px-6 py-4 text-center">
                                {{ $court->capacity ?? '-' }}
                            </td>


                            <td class="px-6 py-4 text-center">

                                <span
                                    class="px-3 py-1 rounded-full text-sm font-bold
                                    {{ $court->is_active ? 'bg-lime-100 text-lime-700' : 'bg-red-100 text-red-700' }}">

                                    {{ $court->is_active ? 'Activa' : 'Inactiva' }}

                                </span>

                            </td>



                            <td class="px-6 py-4 text-right space-x-3">


                                <button wire:click="edit({{ $court->id }})" class="font-bold text-lime-600">

                                    Editar

                                </button>



                                <button wire:click="delete({{ $court->id }})" wire:confirm="¿Eliminar esta cancha?"
                                    class="font-bold text-red-600">

                                    Eliminar

                                </button>


                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">

                                No hay canchas registradas.

                            </td>

                        </tr>
                    @endforelse


                </tbody>


            </table>


        </div>


    </div>


</div>
