<?php

namespace App\Interfaces;

interface RoomRepositoryInterface
{
    public function index($cinemaId, $perPage);
    public function getById($cinemaId, $id);
    public function store(array $data);
    public function update(array $data, $cinemaId, $id);
    public function delete($cinemaId, $id);
}
