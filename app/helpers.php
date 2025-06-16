<?php

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function dd(...$args)
{
    echo '<pre>';
    print_r($args);
    echo '</pre>';
    die();
}

function base_path(string $dir = '')
{
    return dirname(__DIR__) . "/$dir";
}

function array_get(array $arr, string $key, mixed $default = null): mixed
{
    return $arr[$key] ?? $default;
}

function generateJwt(string $subject): string
{
    // subject = user's id
    $payload = [
        'iss' => 'php-drive',  // TODO: on production change to domain name OPTIONAL
        'sub' => $subject,
        'iat' => time(),
        'exp' => time() + (3600 * 24) * 14,  // 14 days
    ];

    $secretKey = $_ENV['JWT_SECRET_KEY'] ?? null;
    if (!isset($secretKey)) {
        throw new \Exception('JWT secret key is not set', 500);
    }
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

function getUserFromJwt(string $jwt)
{
    try {
        $payload = JWT::decode($jwt, new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
        $userId = $payload->sub;  // getting user's id rfom payloads subject

        return User::find($userId);
    } catch (\Exception $e) {
        throw $e;
    }
}

function userDirExists(string $username, string $dirName): bool
{
    return is_dir(base_path("user_store/{$username}/{$dirName}"));
}

function checkUserFileExists(string $username, string $folderName, string $filename): bool
{
    return is_file(base_path("user_store/{$username}/{$folderName}/{$filename}"));
}
