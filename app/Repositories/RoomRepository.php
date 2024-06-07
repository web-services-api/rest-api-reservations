<?php
namespace App\Repositories;

use App\Models\Room;
use App\Interfaces\RoomRepositoryInterface;

class RoomRepository implements RoomRepositoryInterface
{
   public function index($cinemaId, $perPage){
      return Room::where('cinema_id', $cinemaId)->orderBy('id', 'asc')->paginate($perPage);
   }

   public function getById($cinemaId, $id){
      return Room::where('cinema_id', $cinemaId)->find($id);
   }

   public function store(array $data){
      return Room::create($data);
   }

   public function update(array $data, $cinemaId, $id){
      $room = Room::where('cinema_id', $cinemaId)->find($id);
      $room->update($data);
      return $room;
   }
   
   public function delete($cinemaId, $id){
      $room = Room::where('cinema_id', $cinemaId)->find($id);
      $room->delete();
      return $room;
   }
}

