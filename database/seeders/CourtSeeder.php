<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Sport;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $futbol = Sport::where('name', 'Fútbol')->first();
        $padel = Sport::where('name', 'Pádel')->first();
        $voley = Sport::where('name', 'Voley')->first();

        Court::create(['sport_id' => $futbol->id, 'name' => 'Cancha 1', 'description' => 'Fútbol 5', 'price_per_hour' => 15000, 'capacity' => 10, 'is_active' => true]);
        Court::create(['sport_id' => $futbol->id, 'name' => 'Cancha 2', 'description' => 'Fútbol 7', 'price_per_hour' => 20000, 'capacity' => 14, 'is_active' => true]);
        Court::create(['sport_id' => $padel->id, 'name' => 'Cancha 1', 'description' => 'Pádel cubierta', 'price_per_hour' => 12000, 'capacity' => 4, 'is_active' => true]);
        Court::create(['sport_id' => $padel->id, 'name' => 'Cancha 2', 'description' => 'Pádel descubierta', 'price_per_hour' => 10000, 'capacity' => 4, 'is_active' => true]);
        Court::create(['sport_id' => $voley->id, 'name' => 'Cancha 1', 'description' => 'Voley playa', 'price_per_hour' => 8000, 'capacity' => 12, 'is_active' => true]);
    }
}
