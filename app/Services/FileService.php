<?php

namespace App\Services;

use Psr\Http\Message\UploadedFileInterface;

class FileService
{
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

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

    public function saveUploadedFile(string $username, $folderName, UploadedFileInterface $file)
    {
        if (checkUserFileExists($username, $folderName, $file->getClientFilename())) {
            throw new \Exception("{$file->getClientFilename()} already exists in {$folderName}");
        }

        $path = implode(DIRECTORY_SEPARATOR, [
            $this->uploadDir,
            $username,
            $folderName,
            $file->getClientFilename(),
        ]);

        // * MANUALLY WORKING WITH STREEAM
        // $inputStream = $file->getStream()->detach(); // getting native php stream to save file aftewards
        // $outputStream = fopen($path, 'wb');
        // stream_copy_to_stream($inputStream, $outputStream);
        // fclose($inputStream);
        // fclose($outputStream);

        // * USING moveTo() -> simpler
        $file->moveTo($path);

        return $file->getClientFilename();
    }
}
