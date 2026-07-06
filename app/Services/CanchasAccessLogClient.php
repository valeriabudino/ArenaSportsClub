<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CanchasAccessLogClient
{
    /**
     * @return array{date: string, total_scans: int, approved: int, rejected: int, logs: array}
     */
    public function fetch(?string $date = null): array
    {
        $response = Http::withToken(config('services.canchas.token'))
            ->get(config('services.canchas.daily_logs_url'), $date ? ['date' => $date] : [])
            ->throw();

        return $response->json();
    }
}
