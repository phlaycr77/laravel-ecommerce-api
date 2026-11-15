<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function create(array $data);
    public function findByEmail(string $email);
    public function getAllUsers(array $filters = []);
    public function findById(int $id);
    public function updateUser(int $id, array $data);
}