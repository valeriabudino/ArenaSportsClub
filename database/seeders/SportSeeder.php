<?php

namespace Database\Seeders;

use App\Models\Sport;
use Illuminate\Database\Seeder;

class SportSeeder extends Seeder
{
    public function run(): void
    {
        Sport::updateOrCreate(['name' => 'Fútbol'], ['icon' => 'futbol', 'default_start_time' => '08:00', 'default_end_time' => '22:00', 'slot_duration_minutes' => 60]);
        Sport::updateOrCreate(['name' => 'Pádel'], ['icon' => 'padel', 'default_start_time' => '07:00', 'default_end_time' => '23:00', 'slot_duration_minutes' => 90]);
        Sport::updateOrCreate(['name' => 'Voley'], ['icon' => 'voley', 'default_start_time' => '09:00', 'default_end_time' => '21:00', 'slot_duration_minutes' => 60]);
    }
}
