<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function dd(...$args)
{
    echo '<pre>';
    print_r($args);
    echo '</pre>';
    die();
}

function generateJwt(string $subject): string
{
    // subject = user's id
    $payload = [
        'iss' => 'php-drive',  // TODO: on production change to domain name OPTIONAL
        'sub' => $subject,
        'iat' => $iat = time(),
        'exp' => $exp = time() + (3600 * 24) * 14,  // 14 days
    ];

    $secretKey = $_ENV['JWT_SECRET_KEY'] ?? null;
    if (!isset($secretKey))
        throw new \Exception('JWT secret key is not set', 500);
    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    return $jwt;
}

function validateJwt(string $jwt): bool
{
    try {
        // check signature
        JWT::decode($jwt, new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
        return true;
    } catch (\Exception $e) {
        // Token invalid or expired
        return false;
    }
}
