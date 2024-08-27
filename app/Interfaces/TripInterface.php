<?php 
namespace App\Interfaces;

interface TripInterface {
    public function getList(int $page = 1, int $perPage = 10, string $search = null);
    public function insert(array $payload);
    public function update(int $id, array $payload);
    public function delete(int $id);
}