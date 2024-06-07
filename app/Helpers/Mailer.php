<?php

namespace App\Helpers;

use App\Mail\SeanceCreated;
use Illuminate\Support\Facades\Mail;

class Mailer
{
    public static function sendMail($type, $to, $data)
    {
        if($type == 'seanceCreated'){
            $mail = new SeanceCreated($data);
        }
        if($type == 'reservationConfirmed'){
            $mail = new ReservationConfirmed($data);
        }

        Mail::to($to)->send($mail);
    }
}
