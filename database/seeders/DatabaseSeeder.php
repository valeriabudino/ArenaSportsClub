<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Usuario de Prueba',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'arenasportsclub@email.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('arenasport'),
                'role' => 'admin',
            ]
        );

        $this->call([
            SportSeeder::class,
            ClubSettingSeeder::class,
            CourtSeeder::class,
        ]);
    }
}
