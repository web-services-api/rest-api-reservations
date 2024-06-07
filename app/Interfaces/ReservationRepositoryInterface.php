<?php

namespace App\Interfaces;

interface ReservationRepositoryInterface
{
    public function index($movieId, $perPage);
    public function getById($id);
    public function confirm($id);
    public function store(array $data);
    public function update(array $data, $id);
    public function delete($id);
}
