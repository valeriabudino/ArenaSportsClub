<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hasta cuando un turno en pending_payment queda reservado para quien lo
     * inicio. Pasado ese momento se libera (ver Turn::releaseExpiredReservations).
     */
    public function up(): void
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->timestamp('reserved_until')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->dropColumn('reserved_until');
        });
    }
};
