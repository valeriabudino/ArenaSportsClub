<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->dropColumn(['default_start_time', 'default_end_time', 'slot_duration_minutes']);
        });
    }
};
