<?php

namespace App\Services;

use App\Models\File;
use App\Models\Folder;
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
    public function createUserFile(string $username, string $filename, string $folderName): void
    {
        $filePath = "{$this->uploadDir}/{$username}/{$folderName}/{$filename}";
        $file = fopen($filePath, 'w');
        if (!$file) {
            throw new \Exception('Something when wrong when creating file', 500);
        }
        $fileExt = pathinfo($filePath, PATHINFO_EXTENSION);

        // Since first argument always must be passed gotta make it null so it  works
        $this->saveFileToDB(null, filename: $filename, fileExt: $fileExt, folderName: $folderName);

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

        // * USING moveTo() -> simpler; moveTo() just relocates temp file saved (apache or nginx) by server
        // * uploaded file saved to temp file (as temp file) by server and when we moveTo it creates stream
        // * and just relocates that file
        // * upload wasn't loaded into memory, it was sent by web browser chunk by chunk and saved to (as) temp file
        $file->moveTo($path);
        $this->saveFileToDB($file, folderName: $folderName);

        return $file->getClientFilename();
    }

    private function saveFileToDB(
        ?UploadedFileInterface $file,
        ?string $filename,
        ?string $fileExt,
        string $folderName
    ): void {
        $folder = Folder::where('folder_name', $folderName)->first();
        if (!$folder) {
            throw new \Exception("Folder not found: $folderName");
        }

        File::create([
            'filename' => $file ? $file->getClientFilename() : $filename,
            'file_ext' => $file ? $file->getClientMediaType() : $fileExt,
            'file_size' => $file ? $file->getSize() : 0,
            'folder_id' => $folder->id
        ]);
    }
}
