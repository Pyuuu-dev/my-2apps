<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies Cloudflare Turnstile CAPTCHA tokens.
 *
 * Endpoint: https://challenges.cloudflare.com/turnstile/v0/siteverify
 * Docs: https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
 */
class TurnstileVerifier
{
    private const ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    private const TIMEOUT_SECONDS = 5;

    /**
     * Verify a Turnstile response token.
     *
     * Fail-closed: if no secret is configured, verification fails (forces
     * proper configuration in any environment that uses this method). The
     * caller is free to short-circuit before invoking this when running in
     * local/testing without Turnstile.
     */
    public function verify(?string $token, ?string $ip = null): bool
    {
        $secret = (string) config('services.turnstile.secret_key', '');

        if ($secret === '') {
            Log::warning('Turnstile secret_key is not configured; rejecting verification.');
            return false;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $payload = [
                'secret' => $secret,
                'response' => $token,
            ];

            if (!empty($ip)) {
                $payload['remoteip'] = $ip;
            }

            $response = Http::asForm()
                ->timeout(self::TIMEOUT_SECONDS)
                ->post(self::ENDPOINT, $payload);

            if (!$response->successful()) {
                Log::warning('Turnstile verification HTTP error', [
                    'status' => $response->status(),
                ]);
                return false;
            }

            $data = $response->json();
            $success = (bool) ($data['success'] ?? false);

            if (!$success) {
                Log::info('Turnstile verification rejected', [
                    'error_codes' => $data['error-codes'] ?? [],
                    'ip' => $ip,
                ]);
            }

            return $success;
        } catch (\Throwable $e) {
            Log::warning('Turnstile verification exception: ' . $e->getMessage());
            return false;
        }
    }
}
