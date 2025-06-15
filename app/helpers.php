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
        'iat' => $iat = time(),
        'exp' => $exp = time() + (3600 * 24) * 14,  // 14 days
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

function createUserFolder(string $username): string
{
    $userStorePath = base_path('user_store');
    $fullPath = "{$userStorePath}/{$username}";

    if (!is_dir($userStorePath)) {
        throw new \Exception('user_store folder does not exist');
    }

    if (is_dir($fullPath)) {
        throw new \RuntimeException('Folder already exists');
    }

    if (!mkdir($fullPath)) {
        throw new \RuntimeException('Failed to create user folder');
    }
    return $fullPath;
}

function userDirExists(string $username, string $dirName): bool
{
    return is_dir(base_path("user_store/{$username}/{$dirName}"));
}

function createUserDir(string $username, string $dirName): bool
{
    return mkdir(base_path("user_store/{$username}/{$dirName}"));  // since recursive is false only last folder will be created
}

function checkUserFileExists(string $username, string $folderName, string $filename): bool
{
    return is_file(base_path("user_store/{$username}/{$folderName}/{$filename}"));
}

/**
 * utility function for creating user files
 * @param string $username
 * @param string $filename
 * @param string $folderName folder name IN WHICH file will go
 * @return integer
 */
function createUserFile(string $username, string $filename, string $folderName): int
{
    $filePath = base_path("user_store/{$username}/{$folderName}/{$filename}");
    $file = fopen($filePath, 'w');
    if (!$file) {
        return 0;
    }

    fclose($file);
    return 1;
}
