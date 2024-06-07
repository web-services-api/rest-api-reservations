<?php

namespace App\Models;

use App\Models\Seance;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    
    public $incrementing = false;

    protected $fillable = [
        'seance_id',
        'rank',
        'status',
        'seat'
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            $reservation->status = 'open';

            $seance = Seance::find($reservation->seance_id);
            if ($seance) {
                $lastRank = Reservation::where('seance_id', $reservation->seance_id)->where('status', 'open')->max('rank');
                $reservation->rank = $lastRank + 1;
            } else {
                $reservation->rank = 1;
            }
        });
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }
}
