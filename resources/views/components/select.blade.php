{{--
    Desplegable con el estilo de la app. Se usa como un <select> comun:

    <x-select wire:model="sport_id"
        :options="$sports->pluck('name', 'id')"
        placeholder="Seleccionar deporte" />

    - options: [valor => texto]. Una opcion con valor '' sirve como "Todos".
    - Funciona con wire:model y wire:model.live (x-modelable conecta la
      propiedad de Livewire con "value").
    - Las opciones se leen al cargar la pagina; si cambian en un re-render
      de Livewire, poner un wire:key al componente para que se recree.

    El <select> nativo no sirve para esto: la lista abierta la dibuja el
    sistema operativo y no se puede estilar.
--}}
@props([
    'options' => [],
    'placeholder' => 'Seleccionar...',
])

@php
    $items = collect($options)
        ->map(fn ($label, $value) => ['value' => $value, 'label' => (string) $label])
        ->values();

    $listId = 'select-' . \Illuminate\Support\Str::random(8);
@endphp

<div
    x-data="{
        open: false,
        value: null,
        options: @js($items),
        active: 0,

        same(a, b) { return String(a ?? '') === String(b ?? '') },
        get selected() { return this.options.find(o => this.same(o.value, this.value)) ?? null },
        item(index) { return this.$refs.list.querySelectorAll('li')[index] },

        show() {
            this.open = true;
            this.active = Math.max(0, this.options.findIndex(o => this.same(o.value, this.value)));
            this.$nextTick(() => this.item(this.active)?.scrollIntoView({ block: 'nearest' }));
        },
        close() { this.open = false },
        toggle() { this.open ? this.close() : this.show() },

        choose(option) {
            this.value = option.value;
            this.close();
            this.$refs.button.focus();
        },

        move(step) {
            if (! this.open) return this.show();
            this.active = (this.active + step + this.options.length) % this.options.length;
            this.item(this.active)?.scrollIntoView({ block: 'nearest' });
        },
    }"
    x-modelable="value"
    x-on:click.outside="close()"
    x-on:keydown.escape="if (open) { $event.stopPropagation(); close() }"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <button type="button" x-ref="button"
        x-on:click="toggle()"
        x-on:keydown.arrow-down.prevent="move(1)"
        x-on:keydown.arrow-up.prevent="move(-1)"
        x-on:keydown.enter.prevent="open ? choose(options[active]) : show()"
        x-on:keydown.space.prevent="open ? choose(options[active]) : show()"
        x-on:keydown.tab="close()"
        aria-haspopup="listbox"
        aria-controls="{{ $listId }}"
        :aria-expanded="open"
        class="w-full flex items-center justify-between gap-3 rounded-xl border bg-white px-3 py-2 text-left shadow-sm transition focus:outline-none focus:border-lime-400 focus:ring-1 focus:ring-lime-400"
        :class="open ? 'border-lime-400 ring-1 ring-lime-400' : 'border-gray-300'">

        <span class="truncate" :class="selected ? 'text-[#07110d]' : 'text-gray-400'"
            x-text="selected ? selected.label : @js($placeholder)"></span>

        <svg class="h-5 w-5 shrink-0 transition-transform duration-200"
            :class="open ? 'rotate-180 text-lime-600' : 'text-gray-400'"
            fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    <ul x-ref="list" id="{{ $listId }}" role="listbox"
        x-cloak x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute z-30 mt-2 w-full max-h-60 overflow-y-auto rounded-xl bg-white p-1 shadow-xl ring-1 ring-black/5">

        <template x-for="(option, index) in options" :key="String(option.value)">
            <li role="option"
                :aria-selected="same(option.value, value)"
                x-on:click="choose(option)"
                x-on:mouseenter="active = index"
                class="flex items-center justify-between gap-3 rounded-lg px-3 py-2 cursor-pointer transition"
                :class="same(option.value, value)
                    ? 'bg-lime-400 text-black font-bold'
                    : (active === index ? 'bg-gray-100 text-[#07110d]' : 'text-[#07110d]')">

                <span class="truncate" x-text="option.label"></span>

                <svg x-show="same(option.value, value)" class="h-4 w-4 shrink-0"
                    fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </li>
        </template>
    </ul>
</div>
