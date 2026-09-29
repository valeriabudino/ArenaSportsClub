<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Payment;
use App\Models\Turn;

class DashboardController extends Controller
{
    /**
     * Pagos con los que el club se queda: aprobados y cancelaciones con
     * menos de 24hs (no corresponde reembolso). Los refund_pending y
     * refunded no cuentan porque esa plata se devuelve.
     */
    private const INCOME_STATUSES = ['approved', 'cancelled_no_refund'];

    public function __invoke()
    {
        Turn::releaseExpiredReservations();

        $today = today()->toDateString();

        return view('pages.admin.dashboard', [
            'todayBookings' => Turn::where('status', 'booked')->where('date', $today)->count(),
            'upcomingBookings' => Turn::where('status', 'booked')->where('date', '>=', $today)->count(),
            'pendingPayments' => Turn::where('status', 'pending_payment')->where('date', '>=', $today)->count(),
            // Ultimos 30 dias y no "mes actual": asi no queda en $0 a principio de mes.
            'recentIncome' => Payment::whereIn('status', self::INCOME_STATUSES)
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('amount'),
            'activeCourts' => Court::where('is_active', true)->count(),
            'pendingRefunds' => Payment::where('status', 'refund_pending')->count(),
        ]);
    }
}
