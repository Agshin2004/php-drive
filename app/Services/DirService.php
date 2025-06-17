<?php

namespace App\Services;

class DirService
{
    /**
     * Creates a directory for user inside user_store (which is user's folder) folder
     * @param string $username
     * @param string $dirName
     * @return void throws exception if file not created
     */
    public static function createDir(string $username, string $dirName): void
    {
        $created = mkdir(base_path("user_store/{$username}/{$dirName}"));  // since recursive is false only last folder will be created
        
        if (!$created) {
            throw new \Exception('Something went wrong when creating file. Try again');
        }
    }

    public static function createUserFolder($username): string
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
}
