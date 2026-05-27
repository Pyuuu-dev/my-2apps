<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds defense-in-depth HTTP security headers.
 *
 * Headers set:
 *  - Strict-Transport-Security (HTTPS only)
 *  - Content-Security-Policy
 *  - X-Frame-Options
 *  - X-Content-Type-Options
 *  - Referrer-Policy
 *  - Permissions-Policy
 *  - Cross-Origin-Opener-Policy
 *
 * CSP is intentionally permissive on inline scripts/styles to support
 * AlpineJS directives and Tailwind/Vite output without nonces. Tighten
 * later by introducing a nonce pipeline if requirements allow.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // HSTS only over HTTPS — avoid pinning insecure local dev to HTTPS
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload',
                false
            );
        }

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://challenges.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            "frame-src https://challenges.cloudflare.com",
            "connect-src 'self' https://challenges.cloudflare.com",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "worker-src 'self' blob:",
        ]);

        $response->headers->set('Content-Security-Policy', $csp, false);
        $response->headers->set('X-Frame-Options', 'DENY', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), magnetometer=(), gyroscope=()',
            false
        );
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin', false);

        // Remove fingerprinting headers if present
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
