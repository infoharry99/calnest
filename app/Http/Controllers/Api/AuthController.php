<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalnestVerificationCode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * User Registration (POST /api/calnest/register)
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        // Generate 6-digit verification code in case email verification is used
        $verificationCode = (string) random_int(100000, 999999);
        CalnestVerificationCode::create([
            'email' => $user->email,
            'code' => $verificationCode,
            'type' => 'verify_email',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $token = $user->createToken('calnest-auth-token')->plainTextToken;

        return response()->json([
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo_url' => null,
            'token' => $token,
        ], 201);
    }

    /**
     * User Login (POST /api/calnest/login)
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $token = $user->createToken('calnest-auth-token')->plainTextToken;

        return response()->json([
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo_url' => null,
            'token' => $token,
        ], 200);
    }

    /**
     * Current User Profile (GET /api/calnest/me)
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo_url' => null,
        ]);
    }

    /**
     * Update Profile (PUT /api/calnest/me)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['email'])) {
            $user->email = strtolower(trim($validated['email']));
        }
        $user->save();

        return response()->json([
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo_url' => null,
        ]);
    }

    /**
     * Delete Account (DELETE /api/calnest/me) - Apple/Google requirement
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'status' => 'deleted',
        ]);
    }

    /**
     * Logout (POST /api/calnest/logout)
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => 'ok',
        ], 200);
    }

    /**
     * Forgot Password (POST /api/calnest/forgot-password)
     */
    public function forgot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
        ]);

        $email = strtolower(trim($validated['email']));
        $user = User::where('email', $email)->first();

        if ($user) {
            $code = (string) random_int(100000, 999999);
            CalnestVerificationCode::create([
                'email' => $email,
                'code' => $code,
                'type' => 'reset_password',
                'expires_at' => Carbon::now()->addMinutes(15),
            ]);
        }

        return response()->json([
            'status' => 'sent',
        ], 200);
    }

    /**
     * Reset Password (POST /api/calnest/reset-password)
     */
    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $email = strtolower(trim($validated['email']));
        $record = CalnestVerificationCode::where('email', $email)
            ->where('code', trim($validated['code']))
            ->where('type', 'reset_password')
            ->where('expires_at', '>=', Carbon::now())
            ->latest()
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired verification code',
            ], 422);
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($validated['password']);
            $user->save();
            $user->tokens()->delete(); // Revoke all old tokens for security
        }

        CalnestVerificationCode::where('email', $email)->where('type', 'reset_password')->delete();

        return response()->json([
            'status' => 'ok',
        ], 200);
    }

    /**
     * Verify Email (POST /api/calnest/verify-email)
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|string',
        ]);

        $email = strtolower(trim($validated['email']));
        $record = CalnestVerificationCode::where('email', $email)
            ->where('code', trim($validated['code']))
            ->where('type', 'verify_email')
            ->where('expires_at', '>=', Carbon::now())
            ->latest()
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Invalid or expired verification code',
            ], 422);
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->email_verified_at = Carbon::now();
            $user->save();
        }

        CalnestVerificationCode::where('email', $email)->where('type', 'verify_email')->delete();

        return response()->json([
            'status' => 'verified',
        ], 200);
    }

    // --- Google & Microsoft OAuth Endpoints for Backwards-Compatibility ---

    public function googleSignIn(Request $request): JsonResponse
    {
        $email = $request->input('email', 'user@gmail.com');
        $name = $request->input('name', 'Google User');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(Str::random(16)),
            ]
        );

        $token = $user->createToken('google-auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'scope' => 'https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/tasks https://www.googleapis.com/auth/contacts.readonly',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function microsoftAuthorize(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Microsoft OAuth 2.0 Authorization Endpoint',
            'client_id' => $request->query('client_id'),
            'mock_code' => 'MOCK_MS_AUTH_CODE_' . Str::random(24),
        ]);
    }

    public function microsoftToken(Request $request): JsonResponse
    {
        $email = 'ravi@outlook.com';
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Ravi Patel',
                'password' => Hash::make(Str::random(16)),
            ]
        );

        $token = $user->createToken('microsoft-graph-token')->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token,
            'refresh_token' => 'mock_refresh_token_' . Str::random(32),
            'expires_in' => 3600,
            'scope' => 'User.Read Calendars.ReadWrite Tasks.ReadWrite offline_access',
        ]);
    }
}
