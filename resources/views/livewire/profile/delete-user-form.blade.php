<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-2xl font-black text-red-600">
            Eliminar cuenta
        </h2>

        <p class="mt-2 text-sm text-gray-500">
            Una vez eliminada tu cuenta, tus datos se borrarán permanentemente.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-500 text-white px-6 py-3 rounded-xl font-black hover:bg-red-600 transition"
    >
        Eliminar cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">
            <h2 class="text-2xl font-black text-[#07110d]">
                ¿Seguro que querés eliminar tu cuenta?
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Esta acción no se puede deshacer. Ingresá tu contraseña para confirmar.
            </p>

            <div class="mt-6">
                <input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500"
                    placeholder="Contraseña"
                >

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-6 py-3 rounded-xl font-black border border-gray-300 hover:bg-gray-100 transition"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="bg-red-500 text-white px-6 py-3 rounded-xl font-black hover:bg-red-600 transition"
                >
                    Eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>
</section>