<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegisterFormRequest
{
    /**
     * @param  string  $tokenKey     Ключ сессии с токеном формы (default: register_form_token)
     * @param  string  $refererPath  Ожидаемый path реферера (default: /register)
     */
    public function handle(Request $request, Closure $next, string $tokenKey = 'register_form_token', string $refererPath = '/register'): Response
    {
        $sessionToken = (string) $request->session()->get($tokenKey, '');
        $headerToken = (string) $request->header('X-Form-Token', '');

        if ($sessionToken === '' || $headerToken === '' || !hash_equals($sessionToken, $headerToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid form token.',
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
        if (!$referer || !$this->isExpectedReferer($referer, $request, $refererPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request referer.',
            ], 403);
        }

        return $next($request);
    }

    private function isSameOrigin(string $origin, Request $request): bool
    {
        $originHost   = parse_url($origin, PHP_URL_HOST);
        $originScheme = parse_url($origin, PHP_URL_SCHEME);
        $originPort   = parse_url($origin, PHP_URL_PORT);

        return $originHost === $request->getHost()
            && $originScheme === $request->getScheme()
            && (($originPort ?: $this->defaultPort($originScheme)) === $request->getPort());
    }

    private function isExpectedReferer(string $referer, Request $request, string $expectedPath): bool
    {
        $refererHost   = parse_url($referer, PHP_URL_HOST);
        $refererScheme = parse_url($referer, PHP_URL_SCHEME);
        $refererPort   = parse_url($referer, PHP_URL_PORT);
        $refererPath   = parse_url($referer, PHP_URL_PATH) ?: '';

        return $refererHost === $request->getHost()
            && $refererScheme === $request->getScheme()
            && (($refererPort ?: $this->defaultPort($refererScheme)) === $request->getPort())
            && rtrim($refererPath, '/') === rtrim($expectedPath, '/');
    }

    private function defaultPort(?string $scheme): int
    {
        return $scheme === 'https' ? 443 : 80;
    }
}

