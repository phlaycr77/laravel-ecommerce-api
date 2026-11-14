<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface
{
    public function searchAndFilter(array $filters, int $perPage = 10);
    public function getFeatured(int $limit = 8);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}