<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
    */
    public function run(): void
    {   
        $this->call(CinemaSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(SeanceSeeder::class);
        $this->call(ReservationSeeder::class);
    }
}
