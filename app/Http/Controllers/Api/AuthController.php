<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle Google Sign-In / Token verification & exchange.
     */
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

        $token = $user->createToken('google-auth-token', [
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/tasks',
            'https://www.googleapis.com/auth/contacts.readonly',
        ])->plainTextToken;

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

    /**
     * Microsoft OAuth Authorize helper info
     */
    public function microsoftAuthorize(Request $request): JsonResponse
    {
        $clientId = $request->query('client_id');
        $redirectUri = $request->query('redirect_uri');
        $scope = $request->query('scope', 'User.Read Calendars.ReadWrite Tasks.ReadWrite offline_access');

        return response()->json([
            'message' => 'Microsoft OAuth 2.0 Authorization Endpoint',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
            'mock_code' => 'MOCK_MS_AUTH_CODE_' . Str::random(24),
            'instructions' => 'Pass mock_code to POST /consumers/oauth2/v2.0/token to obtain tokens',
        ]);
    }

    /**
     * Microsoft OAuth v2.0 Token Exchange (MSAL)
     */
    public function microsoftToken(Request $request): JsonResponse
    {
        $grantType = $request->input('grant_type');

        if ($grantType !== 'authorization_code' && $grantType !== 'refresh_token') {
            return response()->json([
                'error' => 'invalid_request',
                'error_description' => 'grant_type must be authorization_code or refresh_token',
            ], 400);
        }

        $email = 'ravi@outlook.com';
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Ravi Patel',
                'password' => Hash::make(Str::random(16)),
            ]
        );

        $token = $user->createToken('microsoft-graph-token', [
            'User.Read',
            'Calendars.ReadWrite',
            'Tasks.ReadWrite',
            'offline_access',
        ])->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'scope' => 'User.Read Calendars.ReadWrite Tasks.ReadWrite offline_access',
            'expires_in' => 3600,
            'ext_expires_in' => 3600,
            'access_token' => $token,
            'refresh_token' => 'MOCK_MS_REFRESH_TOKEN_' . Str::random(32),
            'id_token' => 'MOCK_MS_ID_TOKEN_' . Str::random(32),
        ]);
    }
}
