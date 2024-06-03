<?php

namespace App\Interfaces;

interface SeanceRepositoryInterface
{
    public function index($roomId, $perPage);
    public function getById($roomId, $id);
    public function store(array $data, $roomId);
    public function update(array $data, $roomId, $id);
    public function delete($roomId, $id);
}
