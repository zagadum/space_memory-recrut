<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegisterFormRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionToken = (string) $request->session()->get($tokenKey, '');
        $headerToken = (string) $request->header('X-Form-Token', '');

        if ($sessionToken === '' || $headerToken === '' || !hash_equals($sessionToken, $headerToken)) {
        $sessionToken = (string) $request->session()->get('register_form_token', '');
        $headerToken = (string) $request->header('X-Register-Form-Token', '');
            ], 403);
        }

        $origin = $request->headers->get('Origin');
        if ($origin !== null && !$this->isSameOrigin($origin, $request)) {
                'message' => 'Invalid register form token.',
                'success' => false,
                'message' => 'Invalid request origin.',
            ], 403);
        }

        $referer = $request->headers->get('Referer');
        if (!$referer || !$this->isExpectedReferer($referer, $request, $refererPath)) {
            return response()->json([
                'success' => false,
        if (!$referer || !$this->isRegisterReferer($referer, $request)) {
            ], 403);
        }

        return $next($request);
    }

    private function isSameOrigin(string $origin, Request $request): bool
    {
        $originHost   = parse_url($origin, PHP_URL_HOST);
        $originScheme = parse_url($origin, PHP_URL_SCHEME);
        $originPort   = parse_url($origin, PHP_URL_PORT);

        $originHost = parse_url($origin, PHP_URL_HOST);
            && $originScheme === $request->getScheme()
        $originPort = parse_url($origin, PHP_URL_PORT);
    }

    private function isExpectedReferer(string $referer, Request $request, string $expectedPath): bool
    {
        $refererHost   = parse_url($referer, PHP_URL_HOST);
    private function isRegisterReferer(string $referer, Request $request): bool
        $refererPort   = parse_url($referer, PHP_URL_PORT);
        $refererHost = parse_url($referer, PHP_URL_HOST);

        return $refererHost === $request->getHost()
            && rtrim($refererPath, '/') === '/register';
        $refererPort = parse_url($referer, PHP_URL_PORT);
        $refererPath = parse_url($referer, PHP_URL_PATH) ?: '';
            && rtrim($refererPath, '/') === rtrim($expectedPath, '/');
    }

    private function defaultPort(?string $scheme): int
    {
        return $scheme === 'https' ? 443 : 80;
    }
}

