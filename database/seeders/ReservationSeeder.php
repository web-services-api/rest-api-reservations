<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Seance;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seances = Seance::all();
        if(!empty($seances)) {
            foreach($seances as $seance) {
                Reservation::create([
                    'seance_id' => $seance->id,
                    'seat' => rand(20, $seance->room->seats),
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);
            }
        }


    $seance = Seance::first();
    if ($seance) {
        for ($i = 0; $i < 5; $i++) {
            Reservation::create([
                'seance_id' => $seance->id,
                'seat' => rand(20, $seance->room->seats),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
    }
}
