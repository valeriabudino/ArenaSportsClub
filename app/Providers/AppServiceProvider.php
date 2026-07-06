<?php

namespace App\Providers;

use App\Models\Turn;
use App\Models\User;
use App\Observers\TurnObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registre cualquier servicio de aplicación.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar los servicios de la aplicación.
     */
    public function boot(): void
    {
        Gate::define('admin', fn (User $user) => $user->role === 'admin');

        Turn::observe(TurnObserver::class);
    }
}
