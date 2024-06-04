<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Cinema;
use App\Models\Seance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MovieService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SeanceSeeder extends Seeder
{

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limit = 10;
        $movies = $this->movieService->getAll($limit);
        $rooms = Room::all();
        if(!empty($movies) && !empty($rooms)) {
            $i = 0;
            for($i = 0; $i < $limit; $i++) {
                $movie = $movies[$i];
                foreach($rooms as $room) {
                    Seance::create([
                        'movie_id' => $movie['id'],
                        'room_id' => $room->id,
                        'start_date' => Carbon::now()->format('Y-m-d H:i:s'),
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);
                    $i++;
                }
            }
        }
    }
}
