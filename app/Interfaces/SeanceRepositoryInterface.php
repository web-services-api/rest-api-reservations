<?php

namespace App\Interfaces;

interface SeanceRepositoryInterface
{
    public function index($roomId, $perPage);
    public function getById(?string $roomId, string $id);
    public function store(array $data);
    public function update(array $data, $roomId, $id);
    public function delete($roomId, $id);
}
