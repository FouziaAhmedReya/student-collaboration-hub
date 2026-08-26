<?php

namespace App\Services\Tuli;

use App\Models\User;
use Illuminate\Http\Request;

class JwtService
{
    /**
     * Generate a signed HS256 JWT token for the user.
     */
    public static function generateToken(User $user, int $ttlDays = 7): string
    {
        $header = self::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload = self::base64UrlEncode(json_encode([
            'iss' => 'Student-Collaboration-Hub',
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + ($ttlDays * 86400)
        ]));

        $secret = config('app.key') ?: 'default_jwt_secret_key_student_hub_2026';
        $signature = self::base64UrlEncode(hash_hmac('sha256', "{$header}.{$payload}", $secret, true));

        return "{$header}.{$payload}.{$signature}";
    }

    /**
     * Validate and decode a JWT token string.
     */
    public static function decodeToken(string $token): ?array
    {
        $parts = explode('.', trim($token));
        if (count($parts) !== 3) {
            return null;
        }

        list($headerB64, $payloadB64, $signatureB64) = $parts;

        $secret = config('app.key') ?: 'default_jwt_secret_key_student_hub_2026';
        $expectedSignature = self::base64UrlEncode(hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $secret, true));

        if (!hash_equals($expectedSignature, $signatureB64)) {
            return null;
        }

        $payload = json_decode(self::base64UrlDecode($payloadB64), true);
        if (!$payload || !isset($payload['sub']) || !isset($payload['exp'])) {
            return null;
        }

        if ($payload['exp'] < time()) {
            return null; // Expired token
        }

        return $payload;
    }

    /**
     * Resolve the authenticated User model from HTTP Request (Bearer Header, Cookie, Query, or Session).
     */
    public static function getUserFromRequest(Request $request): ?User
    {
        $token = null;

        // 1. Authorization: Bearer <token>
        $header = $request->header('Authorization');
        if (!empty($header) && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            $token = trim($matches[1]);
        }

        // 2. Query param ?token=...
        if (!$token && $request->has('token')) {
            $token = $request->query('token');
        }

        // 3. Cookie jwt_token
        if (!$token && $request->hasCookie('jwt_token')) {
            $token = $request->cookie('jwt_token');
        }

        // 4. Session jwt_token
        if (!$token && session()->has('jwt_token')) {
            $token = session('jwt_token');
        }

        if (!$token) {
            return auth()->user();
        }

        $payload = self::decodeToken($token);
        if (!$payload || !isset($payload['sub'])) {
            return auth()->user();
        }

        return User::with(['profile.skills', 'profile.interests', 'profile.studentProjects'])->find($payload['sub']) ?: auth()->user();
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
