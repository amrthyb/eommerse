<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use App\Notifications\NewUserRegistered;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailOtp;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->password),
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

    try {
        Mail::to($user->email)->send(new SendOtpMail($user));
    } catch (\Exception $e) {

        return response()->json(['message' => __('messageApi.Failed to send OTP email') . $e->getMessage()], 500);
    }

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            sleep(2);
            $admin->notify(new NewUserRegistered($user));
        }
        return response()->json(
            [
                'ok' => $user,
                'message' => __('messageApi.User created successfully'),
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

        return response()->json(
            [
                'success' => false,
                'message' => __('messageApi.User creation failed'),
            ],
            400,
        );
    }

    public function forgotPassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    $otp = rand(100000, 999999);
    $user->otp_code = $otp;
    $user->otp_expires_at = now()->addMinutes(15);
    $user->save();

    Mail::raw("Kode OTP Anda adalah: $otp", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Reset Password OTP');
    });

    return response()->json(['message' => __('messageApi.OTP has been sent to your email')]);
}

public function updatePassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'otp' => 'required|numeric',
        'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::where('otp_code', $request->otp)->first();

    if (now()->gt($user->otp_expires_at)) {
        return response()->json(['error' => __('messageApi.OTP is invalid or expired')], 403);
    }

    $user->password = Hash::make($request->password);
    $user->otp_code = null;
    $user->otp_expires_at = null;
    $user->save();

    return response()->json(['message' => __('messageApi.Password has been reset successfully.')]);
}

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => __('messageApi.user not found')], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => __('messageApi.Email already verified')], 200);
        }

        if ($user->otp_code == $request->otp && now()->lt($user->otp_expires_at)) {
            $user->email_verified_at = now();
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json(['message' => __('messageApi.Email verified successfully')], 200);
        }

        return response()->json(['message' => __('messageApi.Invalid or expired OTP')], 400);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:128',
            'password' => 'required|min:6|max:28',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();
        // Dd($user);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => __('messageApi.Invalid credentials'),
                ],
                401,
            );
        }

        if (!$user->email_verified_at) {
        $otp = rand(100000, 999999);
        $user->update(['otp_code' => $otp,'otp_expires_at' => Carbon::now()->addMinutes(15)]);
            Mail::to($user->email)->send(new SendOtpMail($user));

            return response()->json(
                [
                    'message' => __('messageApi.Please verify your email first'),
                ],
                403,
            );
        }

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
        $user = $request->user();
        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => __('messageApi.User not authenticated'),
                ],
                401,
            );
        }

        if ($request->has('email')) {
            $existingEmail = User::where('email', $request->email)->first();

            if ($existingEmail && $existingEmail->id != $user->id) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => __('messageApi.The email address is already in use by another user'),
                    ],
                    400,
                );
            }

            if ($existingEmail && $existingEmail->id == $user->id) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => __('messageApi.You are still using the same email address'),
                    ],
                    200,
                );
            }
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|numeric|min:10',
        ]);

        if ($validated) {
            if ($user->email != $request->email) {
                $otp = rand(100000, 999999);
                $validated['otp_code'] = $otp;
                $validated['otp_expires_at'] = Carbon::now()->addMinutes(15);
                $validated['email_verified_at'] = null;
                $user->update($validated);
                Mail::to($user->email)->send(new SendOtpMail($user));
                return response()->json(
                    [
                        'message' => __('messageApi.Please verify your email first'),
                    ],
                    403,
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => __('messageApi.update success'),
                ],
                200,
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => __('messageApi.Validation failed'),
            ],
            422,
        );
    }

    public function logout()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => __('messageApi.User not authenticated'),
                ],
                401,
            );
        }

        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'success' => true,
            'message' =>__('messageApi.logout success'),
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json($user);
    }
}
