<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth('api')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
        ]);

        $user->update(['name' => $validated['name']]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Cập nhật thông tin cá nhân thành công.',
            'data'    => $user,
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = auth('api')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mật khẩu hiện tại không chính xác.',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Đổi mật khẩu thành công.',
        ]);
    }
}