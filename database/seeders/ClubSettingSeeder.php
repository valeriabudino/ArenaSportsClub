<?php

namespace Database\Seeders;

use App\Models\ClubSetting;
use Illuminate\Database\Seeder;

class ClubSettingSeeder extends Seeder
{
    public function run(): void
    {
        ClubSetting::create([
            'name' => 'ArenaSportsClub',
            'description' => 'Complejo deportivo con canchas de fútbol y pádel.',
            'email' => 'arenasportsclub@email.com',
            'phone' => '+54 370 000 0000',
            'address' => 'Formosa, Argentina',
            'opening_time' => '08:00',
            'closing_time' => '23:00',
            'instagram' => null,
            'facebook' => null,
        ]);
    }
}