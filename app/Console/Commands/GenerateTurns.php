<?php

namespace App\Console\Commands;

use App\Models\Court;
use App\Models\Turn;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTurns extends Command
{
    protected $signature = 'turns:generate {--days=30}';

    protected $description = 'Genera turnos disponibles para cada cancha';

    public function handle()
    {
        $days = (int) $this->option('days');
        $created = 0;
        $skipped = 0;

        $courts = Court::with('sport')->where('is_active', true)->get();

        foreach ($courts as $court) {
            $sport = $court->sport;
            $start = Carbon::parse($sport->default_start_time ?? '08:00');
            $end = Carbon::parse($sport->default_end_time ?? '22:00');
            $duration = (int) ($sport->slot_duration_minutes ?? 60);

            for ($day = 0; $day < $days; $day++) {
                $date = Carbon::today()->addDays($day);

                $slotStart = $start->copy();
                while ($slotStart->copy()->addMinutes($duration)->lessThanOrEqualTo($end)) {
                    $slotEnd = $slotStart->copy()->addMinutes($duration);

                    $exists = Turn::where('court_id', $court->id)
                        ->where('date', $date->toDateString())
                        ->where('start_time', $slotStart->format('H:i:s'))
                        ->exists();

                    if (! $exists) {
                        Turn::create([
                            'court_id' => $court->id,
                            'date' => $date->toDateString(),
                            'start_time' => $slotStart->format('H:i:s'),
                            'end_time' => $slotEnd->format('H:i:s'),
                            'price' => $court->price_per_hour,
                            'status' => 'available',
                        ]);
                        $created++;
                    } else {
                        $skipped++;
                    }

                    $slotStart = $slotEnd;
                }
            }
        }

        $this->info("Creados: {$created} turnos | Omitidos (ya existían): {$skipped}");
    }
}