<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = $data['role'] ?? 'customer'; // Mặc định là customer

        return $this->userRepository->create($data);
    }

    public function login(array $credentials)
    {
        if (!$token = auth('api')->attempt($credentials)) {
            return null;
        }

        return $this->respondWithToken($token);
    }

   protected function respondWithToken($token)
    {
    return [
        'access_token' => $token,
        'token_type'   => 'bearer',
        'expires_in'   => JWTAuth::factory()->getTTL() * 60,
        'user'         => auth('api')->user(),
    ];
    }
    public function me()
    {
        return auth('api')->user();
    }

    public function logout()
    {
        auth('api')->logout();
        return true;
    }
}