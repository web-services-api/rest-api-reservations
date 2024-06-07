<?php
namespace App\Repositories;

use App\Models\Reservation;
use App\Interfaces\ReservationRepositoryInterface;

class ReservationRepository implements ReservationRepositoryInterface
{
   public function index($movieId, $perPage){
      return Reservation::whereHas('seance', function ($query) use ($movieId) {
         $query->where('movie_id', $movieId);
      })->orderBy('id', 'asc')->paginate($perPage);
   }

   public function getById($id){
      return Reservation::find($id);
   }

   public function confirm($id){
      $reservation = Reservation::find($id);
      $reservation->update(['status' => 'confirmed']);
      return $reservation;
   }
   
   // /movie/{movieUid}/reservations
   public function store(array $data){
      return Reservation::create($data);
   }

   public function update(array $data, $id){
      $reservation = Reservation::find($id);
      $reservation->update($data);
      return $reservation;
   }
   
   public function delete($id){
      $reservation = Reservation::find($id);
      $reservation->delete();
      return $reservation;
   }
}

