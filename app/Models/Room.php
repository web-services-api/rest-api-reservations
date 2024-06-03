<?php

namespace App\Models;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    
    public $incrementing = false;

    protected $fillable = [
        'name',
        'seats',
        'cinema_id'
    ];


    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }
}
