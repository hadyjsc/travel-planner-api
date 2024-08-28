<?php 
namespace App\Interfaces;

interface UserInterface {
    public function insert(array $payload);
    public function selectUser(array $payload);
}