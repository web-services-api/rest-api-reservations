<?php

namespace App\Models;

use App\Models\Room;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seance extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    
    public $incrementing = false;

    protected $fillable = [
        'movie_id',
        'room_id',
        'start_date'
    ];


    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
