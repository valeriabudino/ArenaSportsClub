<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component {
    //
}; ?>


<div class="p-8">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900">
            Configuración del club
        </h1>

        <p class="text-gray-500 mt-2">
            Administrá la información principal de ArenaSportsClub.
        </p>
    </div>


    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow p-8">

        <form class="grid md:grid-cols-2 gap-6">


            {{-- Nombre --}}
            <div>
                <label class="font-bold text-sm text-gray-700">
                    Nombre del club
                </label>

                <input type="text" value="ArenaSportsClub"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>



            {{-- Email --}}
            <div>
                <label class="font-bold text-sm text-gray-700">
                    Email
                </label>

                <input type="email" value="arenasportsclub@email.com"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>



            {{-- Teléfono --}}
            <div>
                <label class="font-bold text-sm text-gray-700">
                    WhatsApp
                </label>

                <input type="text" value="+54 370 000 0000"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>



            {{-- Dirección --}}
            <div>
                <label class="font-bold text-sm text-gray-700">
                    Dirección
                </label>

                <input type="text" value="Formosa, Argentina"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>



            {{-- Apertura --}}
            <div>
                <label class="font-bold text-sm text-gray-700">
                    Horario apertura
                </label>

                <input type="time" value="08:00"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>



            {{-- Cierre --}}
            <div>
                <label class="font-bold text-sm text-gray-700">
                    Horario cierre
                </label>

                <input type="time" value="23:00"
                    class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">
            </div>



            {{-- Descripción --}}
            <div class="md:col-span-2">

                <label class="font-bold text-sm text-gray-700">
                    Descripción
                </label>


                <textarea rows="4" class="mt-2 w-full rounded-xl border-gray-300 focus:border-lime-400 focus:ring-lime-400">Complejo deportivo con canchas de fútbol y pádel.</textarea>

            </div>



            <div class="md:col-span-2 flex justify-end">

                <button type="button" class="bg-lime-400 px-8 py-3 rounded-xl font-black hover:bg-lime-300 transition">

                    Guardar cambios

                </button>

            </div>


        </form>

    </div>

</div>
