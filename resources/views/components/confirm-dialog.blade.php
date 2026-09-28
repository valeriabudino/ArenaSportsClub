{{--
    Modal de confirmacion reutilizable. Hay uno solo por pagina (esta en los
    layouts admin y public) y cualquier boton lo abre despachando el evento
    "confirm-action":

    <button type="button" x-on:click="$dispatch('confirm-action', {
        title: '¿Eliminar cancha?',
        message: 'Esta acción no se puede deshacer.',   // opcional
        confirmText: 'Eliminar',                        // opcional, default "Confirmar"
        cancelText: 'Volver',                           // opcional, default "Cancelar"
        variant: 'danger',                              // 'danger' (rojo) o 'primary' (lima)
        onConfirm: () => $wire.delete(1),               // lo que se ejecuta al confirmar
    })">

    Si el texto trae datos de la base (ej. el nombre de una cancha), pasarlo
    con @js(...) para que comillas o caracteres raros no rompan el JS.
--}}
<div
    x-data="{
        title: '',
        message: '',
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        variant: 'danger',
        onConfirm: null,

        ask(detail) {
            this.title = detail.title ?? '¿Estás seguro?';
            this.message = detail.message ?? '';
            this.confirmText = detail.confirmText ?? 'Confirmar';
            this.cancelText = detail.cancelText ?? 'Cancelar';
            this.variant = detail.variant ?? 'danger';
            this.onConfirm = detail.onConfirm ?? null;
            this.$dispatch('open-modal', 'confirm-action');
        },

        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.$dispatch('close-modal', 'confirm-action');
        },
    }"
    x-on:confirm-action.window="ask($event.detail)">

    <x-modal name="confirm-action" max-width="md" focusable>
        <div class="p-8 text-[#07110d]">

            <div class="flex items-start gap-4">

                {{-- Icono segun la variante --}}
                <div class="shrink-0 flex h-12 w-12 items-center justify-center rounded-2xl"
                    :class="variant === 'danger' ? 'bg-red-100 text-red-600' : 'bg-lime-100 text-lime-700'">
                    <svg x-show="variant === 'danger'" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <svg x-show="variant !== 'danger'" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h2 class="text-2xl font-black" x-text="title"></h2>
                    <p x-show="message" x-text="message" class="mt-2 text-sm text-gray-600"></p>
                </div>

            </div>

            <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" x-text="cancelText"
                    class="px-6 py-3 rounded-xl font-black border border-gray-300 text-[#07110d] hover:bg-gray-100 transition">
                </button>

                <button type="button" x-on:click="confirm()" x-text="confirmText"
                    class="px-6 py-3 rounded-xl font-black transition"
                    :class="variant === 'danger' ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-lime-400 text-black hover:bg-lime-300'">
                </button>
            </div>

        </div>
    </x-modal>
</div>
