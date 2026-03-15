<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegisterFormRequest
{
    public function handle(
        Request $request,
        Closure $next,
        string $tokenKey = 'register_form_token',
        string $refererPath = '/register'
    ): Response {
        $sessionToken = (string) $request->session()->get($tokenKey, '');
        $headerToken = $this->resolveHeaderToken($request);

        if ($sessionToken === '' || $headerToken === '' || !hash_equals($sessionToken, $headerToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid register form token.',
            ], 403);
        }

        $origin = $request->headers->get('Origin');
        if ($origin !== null && !$this->isSameOrigin($origin, $request)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request origin.',
            ], 403);
        }

        $referer = $request->headers->get('Referer');
        if ($referer === null || !$this->isExpectedReferer($referer, $request, $refererPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request origin.',
            ], 403);
        }

        return $next($request);
    }

    private function resolveHeaderToken(Request $request): string
    {
        $candidates = [
            (string) $request->header('X-Form-Token', ''),
            (string) $request->header('X-Register-Form-Token', ''),
            (string) $request->header('X-Verify-Form-Token', ''),
        ];

        foreach ($candidates as $token) {
            if ($token !== '') {
                return $token;
            }
        }

        return '';
    }

    private function isSameOrigin(string $origin, Request $request): bool
    {
        $originHost = parse_url($origin, PHP_URL_HOST);
        $originScheme = parse_url($origin, PHP_URL_SCHEME);
        $originPort = parse_url($origin, PHP_URL_PORT);

        if ($originHost === null || $originScheme === null) {
            return false;
        }

        return $originHost === $request->getHost()
            && strtolower($originScheme) === strtolower($request->getScheme())
            && ($originPort ?? $this->defaultPort($originScheme)) === $request->getPort();
    }

    private function isExpectedReferer(string $referer, Request $request, string $expectedPath): bool
    {
        $refererHost = parse_url($referer, PHP_URL_HOST);
        $refererScheme = parse_url($referer, PHP_URL_SCHEME);
        $refererPort = parse_url($referer, PHP_URL_PORT);
        $refererPath = parse_url($referer, PHP_URL_PATH) ?: '';

        if ($refererHost === null || $refererScheme === null) {
            return false;
        }

        return $refererHost === $request->getHost()
            && strtolower($refererScheme) === strtolower($request->getScheme())
            && ($refererPort ?? $this->defaultPort($refererScheme)) === $request->getPort()
            && rtrim($refererPath, '/') === rtrim($expectedPath, '/');
    }

    private function defaultPort(?string $scheme): int
    {
        return strtolower((string) $scheme) === 'https' ? 443 : 80;
    }
}

