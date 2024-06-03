<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Cinema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cinemas = Cinema::all();
        $i = 1;
        foreach($cinemas as $cinema) {
            Room::create([
                'name' => 'room ' . $i,
                'seats' => rand(20, 100),
                'cinema_id' => $cinema->id,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
            $i++;
        }
    }
}
