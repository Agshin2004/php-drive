<?php

namespace App\Services;

class FileService
{
    /**
     * utility function for creating user files
     * @param string $username
     * @param string $filename
     * @param string $folderName folder name IN WHICH file will go
     * @return integer
     */
    public static function createUserFile(string $username, string $filename, string $folderName): void
    {
        $filePath = base_path("user_store/{$username}/{$folderName}/{$filename}");
        $file = fopen($filePath, 'w');
        if (!$file) {
            throw new \Exception('Something when wrong when creating file', 500);
        }

        fclose($file);
    }
}
