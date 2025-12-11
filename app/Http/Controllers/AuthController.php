<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrasi user baru dan kirimkan token JWT.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $this->guard()->login($user);

        return $this->respondWithToken($token);
    }

    /**
     * Autentikasi user dan kembalikan token JWT.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = $this->guard()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Ambil profil user berdasarkan token yang aktif.
     */
    public function me(): JsonResponse
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Logout sekaligus blacklist token aktif.
     */
    public function logout(): JsonResponse
    {
        $this->guard()->logout();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    /**
     * Bentuk respons standar token JWT.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => $this->guard()->user(),
        ]);
    }

    /**
     * Gunakan guard JWT bawaan.
     */
    protected function guard()
    {
        return auth('api');
    }
}
