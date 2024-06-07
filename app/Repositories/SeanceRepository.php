<?php
namespace App\Repositories;

use App\Models\Seance;
use App\Interfaces\SeanceRepositoryInterface;

class SeanceRepository implements SeanceRepositoryInterface
{
   public function index($roomId, $perPage){
      return Seance::where('room_id', $roomId)->orderBy('id', 'asc')->paginate($perPage);
   }

   public function getById(?string $roomId, string $id){
      if($roomId){
         return Seance::where('room_id', $roomId)->find($id);
      }
      return Seance::find($id);
   }

   public function store(array $data){
      return Seance::create($data);
   }

   public function update(array $data, $roomId, $id){
      $seance = Seance::where('room_id', $roomId)->find($id);
      $seance->update($data);
      return $seance;
   }
   
   public function delete($roomId, $id){
      $seance = Seance::where('room_id', $roomId)->find($id);
      $seance->delete();
      return $seance;
   }
}

