<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    /**
     * Создать JWT-токен
     *
     * @param array $payload
     * @param string $secret
     * @param string $algorithm
     * @return string
     */
    public static function createToken(array $payload, string $secret, string $algorithm = 'HS256'): string
    {
        return JWT::encode($payload, $secret, $algorithm);
    }

    /**
     * Декодировать JWT-токен
     *
     * @param string $token
     * @param string $secret
     * @param string $algorithm
     * @return object
     */
    public static function decodeToken(string $token, string $secret, string $algorithm = 'HS256'): object
    {
        return JWT::decode($token, new Key($secret, $algorithm));
    }
}
