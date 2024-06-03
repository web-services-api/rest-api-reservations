<?php
namespace App\Repositories;

use App\Models\Cinema;
use App\Interfaces\CinemaRepositoryInterface;

class CinemaRepository implements CinemaRepositoryInterface
{
   public function index($perPage){
      return Cinema::orderBy('id', 'asc')->paginate($perPage);
   }

   public function getById($id){
      return Cinema::find($id);
   }

   public function store(array $data){
      return Cinema::create($data);
   }

   public function update(array $data,$id){
      $cinema = Cinema::find($id);
      $cinema->update($data);
      return $cinema;
   }
   
   public function delete($id){
      return Cinema::destroy($id);
   }
}

