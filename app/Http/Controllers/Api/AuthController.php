<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'customer',
        ]);

        // Gửi email xác thực
        Mail::to($user->email)->send(new VerifyEmail($user));

        return response()->json([
            'message' => 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Kiểm tra email đã được xác thực chưa
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Vui lòng xác thực email trước khi đăng nhập. Kiểm tra email của bạn để tìm link xác thực.',
                'email_verified' => false,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email đã được xác thực trước đó.',
            ], 200);
        }

        if (sha1($user->email) !== $hash) {
            return response()->json([
                'message' => 'Link xác thực không hợp lệ.',
            ], 400);
        }

        if (!hash_equals((string) $id, (string) $user->getKey())) {
            return response()->json([
                'message' => 'Link xác thực không hợp lệ.',
            ], 400);
        }

        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Link xác thực đã hết hạn. Vui lòng yêu cầu gửi lại email xác thực.',
            ], 400);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email đã được xác thực thành công!',
        ], 200);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email đã được xác thực rồi.',
            ], 200);
        }

        Mail::to($user->email)->send(new VerifyEmail($user));

        return response()->json([
            'message' => 'Đã gửi lại email xác thực. Vui lòng kiểm tra hộp thư của bạn.',
        ], 200);
    }
}
