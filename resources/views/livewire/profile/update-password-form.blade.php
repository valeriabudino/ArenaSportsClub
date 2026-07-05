<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-2xl font-black text-[#07110d]">
            Seguridad
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            Actualizá tu contraseña para mantener tu cuenta protegida.
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-5">
        <div>
            <label class="font-bold text-sm text-gray-700">Contraseña actual</label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password"
                   class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                   autocomplete="current-password">
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="font-bold text-sm text-gray-700">Nueva contraseña</label>
            <input wire:model="password" id="update_password_password" name="password" type="password"
                   class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                   autocomplete="new-password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label class="font-bold text-sm text-gray-700">Confirmar contraseña</label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400"
                   autocomplete="new-password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-lime-400 text-black px-6 py-3 rounded-xl font-black hover:bg-lime-300 transition">
                Guardar contraseña
            </button>

            <x-action-message class="me-3 font-bold text-lime-600" on="password-updated">
                Guardado.
            </x-action-message>
        </div>
    </form>
</section>