<?php

namespace App\Interfaces;

interface CinemaRepositoryInterface
{
    public function index($perPage);
    public function getById($id);
    public function store(array $data);
    public function update(array $data,$id);
    public function delete($id);
}
