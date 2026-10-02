{{--
    Campo de contraseña con el boton para mostrarla u ocultarla, igual que
    en el login. Los atributos (wire:model, id, autocomplete...) van al input.

    <x-auth.password-input wire:model="password" id="password" label="Contraseña nueva" autocomplete="new-password" />
--}}
@props([
    'label' => 'Contraseña',
    'placeholder' => '',
    'error' => null,
])

<div x-data="{ show: false }">
    <label for="{{ $attributes->get('id') }}" class="font-bold text-sm text-gray-700">
        {{ $label }}
    </label>

    <div class="relative mt-2">
        <input
            {{ $attributes->merge(['class' => 'w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400 pr-12']) }}
            :type="show ? 'text' : 'password'"
            type="password"
            placeholder="{{ $placeholder }}"
            required
        >

        <button type="button" x-on:click="show = !show"
            :aria-label="show ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-lime-500 transition">
            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z" />
                <circle cx="12" cy="12" r="3" />
            </svg>

            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.6 10.6A3 3 0 0012 15a3 3 0 002.4-4.8" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 5.4A10.8 10.8 0 0112 5c6 0 10 7 10 7a16 16 0 01-3.1 3.9" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.1 6.1C3.5 7.8 2 12 2 12s4 7 10 7a10.8 10.8 0 004.2-.9" />
            </svg>
        </button>
    </div>

    @if ($error)
        <x-input-error :messages="$errors->get($error)" class="mt-2" />
    @endif
</div>
