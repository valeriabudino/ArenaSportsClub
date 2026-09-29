<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    /**
     * Minutos que tiene un usuario para pagar un turno antes de que se libere.
     */
    public const PAYMENT_WINDOW_MINUTES = 15;

    protected $fillable = [
        'court_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'price',
        'status',
        'reserved_until',
        'qr_code',
        'reminder_sent_at',
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Libera los turnos pendientes de pago cuyo plazo vencio (o que no tienen
     * plazo, como los creados antes de que existiera): vuelven a "available"
     * y sus pagos pendientes pasan a "expired".
     *
     * Se llama antes de mostrar o reservar turnos, asi la app no depende de
     * un proceso programado para liberarlos. Devuelve cuantos libero.
     */
    public static function releaseExpiredReservations(): int
    {
        $expired = fn ($query) => $query
            ->where('status', 'pending_payment')
            ->where(fn ($q) => $q->whereNull('reserved_until')->orWhere('reserved_until', '<', now()));

        $ids = static::query()->tap($expired)->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        Payment::whereIn('turn_id', $ids)->where('status', 'pending')->update(['status' => 'expired']);

        // Se repiten las condiciones por si alguno se volvio a reservar entre
        // la consulta de arriba y este update.
        return static::query()->whereIn('id', $ids)->tap($expired)->update([
            'status' => 'available',
            'user_id' => null,
            'reserved_until' => null,
            'qr_code' => null,
            'reminder_sent_at' => null,
        ]);
    }
}
