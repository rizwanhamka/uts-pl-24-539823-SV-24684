<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            Log::warning('Login gagal', [
                'actor' => Actor::label($user),
                'action' => 'auth.login',
                'reason' => $user ? 'wrong_password' : 'user_not_found',
            ]);

            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        Log::info('Login berhasil', [
            'actor' => Actor::label($user),
            'action' => 'auth.login',
        ]);

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Log::info('Logout', [
            'actor' => Actor::label($request->user()),
            'action' => 'auth.logout',
        ]);

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil.']);
    }
}
