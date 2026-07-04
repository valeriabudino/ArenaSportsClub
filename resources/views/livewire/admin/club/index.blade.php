<?php

use App\Models\ClubSetting;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component {
    public $setting;

    public $name = '';
    public $description = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $opening_time = '';
    public $closing_time = '';
    public $instagram = '';
    public $facebook = '';

    public function mount(): void
    {
        $this->setting = ClubSetting::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'ArenaSportsClub',
                'description' => 'Complejo deportivo con canchas de fútbol y pádel.',
                'email' => 'arenasportsclub@email.com',
                'phone' => '+54 370 000 0000',
                'address' => 'Formosa, Argentina',
                'opening_time' => '08:00',
                'closing_time' => '23:00',
            ]
        );

        $this->name = $this->setting->name;
        $this->description = $this->setting->description;
        $this->email = $this->setting->email;
        $this->phone = $this->setting->phone;
        $this->address = $this->setting->address;
        $this->opening_time = $this->setting->opening_time;
        $this->closing_time = $this->setting->closing_time;
        $this->instagram = $this->setting->instagram;
        $this->facebook = $this->setting->facebook;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
        ]);

        $this->setting->update([
            'name' => $this->name,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'instagram' => $this->instagram,
            'facebook' => $this->facebook,
        ]);

        session()->flash('success', 'Configuración del club actualizada correctamente.');
    }
}; ?>


<div>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">
            Configuración del club
        </h1>

        <p class="text-gray-500 mt-2">
            Administrá la información principal de ArenaSportsClub.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-lime-200 bg-lime-50 px-5 py-4 text-lime-700 font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow p-8">
        <form wire:submit="save" class="grid md:grid-cols-2 gap-6">

            <div>
                <label class="font-bold text-sm text-gray-700">Nombre del club</label>
                <input wire:model="name" type="text"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">Email</label>
                <input wire:model="email" type="email"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">WhatsApp</label>
                <input wire:model="phone" type="text"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">Dirección</label>
                <input wire:model="address" type="text"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">Horario apertura</label>
                <input wire:model="opening_time" type="time"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">Horario cierre</label>
                <input wire:model="closing_time" type="time"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">Instagram</label>
                <input wire:model="instagram" type="text"
                    placeholder="https://instagram.com/..."
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <div>
                <label class="font-bold text-sm text-gray-700">Facebook</label>
                <input wire:model="facebook" type="text"
                    placeholder="https://facebook.com/..."
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>

            <div class="md:col-span-2">
                <label class="font-bold text-sm text-gray-700">Descripción</label>
                <textarea wire:model="description" rows="4"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"></textarea>
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                    class="bg-lime-400 px-8 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                    Guardar cambios
                </button>
            </div>

        </form>
    </div>
</div>