<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->time('default_start_time')->default('08:00');
            $table->time('default_end_time')->default('22:00');
            $table->unsignedSmallInteger('slot_duration_minutes')->default(60);
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->dropColumn(['default_start_time', 'default_end_time', 'slot_duration_minutes']);
        });
    }
};
