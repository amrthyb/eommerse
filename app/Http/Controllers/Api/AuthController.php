<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegistered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->password),
        ]);
        // Kirim notifikasi ke user & admin
        $user->notify(new NewUserRegistered($user));
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewUserRegistered($user));
        }
        return response()->json(
            [
                'ok' => $user,
            ],
            201,
        );

        if ($user) {
            return response()->json(
                [
                    'success' => true,
                    'user' => $user->makeHidden(['password']),
                ],
                201,
            );
        }

        //return JSON process insert failed
        return response()->json(
            [
                'success' => false,
                'message' => 'User creation failed.',
            ],
            400,
        );
    }

    public function login(Request $request): JsonResponse
    {
        // Validate the login input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:128',
            'password' => 'required|min:6|max:28',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();
        // Dd($user);

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ],
                401,
            );
        }

        // Create token for the user
        $token = $user->createToken('ApiToken')->plainTextToken;

        return response()->json(
            [
                'success' => true,
                'user' => $user->makeHidden(['password']),
                'token' => $token,
            ],
            200,
        );
    }

    public function update(Request $request)
    {
        // Pastikan user sudah terautentikasi
        $user = $request->user();
        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User not authenticated.',
                ],
                401,
            );
        }

        // Cek apakah email sudah ada di database dan digunakan oleh pengguna lain
        if ($request->has('email')) {
            $existingEmail = User::where('email', $request->email)->first();

            // Jika email sudah digunakan oleh pengguna lain yang bukan diri kita sendiri
            if ($existingEmail && $existingEmail->id != $user->id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'The email address is already in use by another user.',
                    ],
                    400,
                );
            }

            // Jika email yang dimasukkan sama dengan email pengguna yang sedang login
            if ($existingEmail && $existingEmail->id == $user->id) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'You are still using the same email address.',
                    ],
                    200,
                );
            }
        }

        // Validasi input yang diterima dari request
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|numeric|min:10',
        ]);

        // Jika ada data yang perlu diupdate
        if ($validated) {
            $user->update($validated);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'User details updated successfully.',
                    'data' => $user,
                ],
                200,
            );
        }

        // Jika validasi gagal
        return response()->json(
            [
                'success' => false,
                'message' => 'Validation failed.',
            ],
            422,
        );
    }

    public function logout()
    {
        // Pastikan user sudah terautentikasi
        $user = Auth::user();
        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'User not authenticated.',
                ],
                401,
            );
        }

        // Revoke all tokens dari user
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    // Method untuk mengambil data pengguna yang sedang login
    public function me(Request $request)
    {
        // Mengambil data pengguna yang sedang login
        $user = $request->user();

        return response()->json($user);
    }
}
