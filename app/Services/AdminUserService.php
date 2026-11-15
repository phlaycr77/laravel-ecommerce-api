<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Exception;

class AdminUserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepo
    ) {}

    public function listUsers(array $filters = [])
    {
        return $this->userRepo->getAllUsers($filters);
    }

    public function toggleUserStatus(int $adminId, int $targetUserId, bool $isActive)
    {
        if ($adminId === $targetUserId) {
            throw new Exception('Không thể tự khóa tài khoản quản trị của chính mình.', 400);
        }

        $user = $this->userRepo->findById($targetUserId);

        if ($user->role === 'admin') {
            throw new Exception('Không thể khóa tài khoản quản trị viên khác.', 403);
        }

        return $this->userRepo->updateUser($targetUserId, ['is_active' => $isActive]);
    }
}