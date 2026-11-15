<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }
    public function getAllUsers(array $filters = [])
    {
        $query = \App\Models\User::select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->orderByDesc('id');

        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                ->orWhere('email', 'like', "%{$kw}%");
            });
        }

        return $query->paginate(15);
    }

    public function findById(int $id)
    {
        return \App\Models\User::findOrFail($id);
    }

    public function updateUser(int $id, array $data)
    {
    $user = \App\Models\User::findOrFail($id);
    $user->update($data);
    return $user;
    }
}