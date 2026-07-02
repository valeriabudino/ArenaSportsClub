<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.app')] class extends Component
{
    public function logout(): void
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">Bienvenido, {{ auth()->user()->name }}. Tienes permisos de administrador.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <h3 class="font-semibold text-indigo-800">Usuarios</h3>
                            <p class="text-sm text-gray-600 mt-1">Gestionar usuarios del sistema</p>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <h3 class="font-semibold text-indigo-800">Roles</h3>
                            <p class="text-sm text-gray-600 mt-1">Administrar roles y permisos</p>
                        </div>

                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <h3 class="font-semibold text-indigo-800">Configuración</h3>
                            <p class="text-sm text-gray-600 mt-1">Ajustes generales del sistema</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
