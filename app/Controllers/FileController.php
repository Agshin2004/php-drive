<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\FileService;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FileController
{
    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    // public function getUserFiles(Request $request, Response $response)
    // {
    // $user = $request->getAttribute('user');
    // File::where('')
    // }

    public function createFile(Request $request, Response $response)
    {
        $parsed = $request->getParsedBody();
        $filename = trim(array_get($parsed, 'filename'));
        $folderName = array_get($parsed, 'folder_name');
        $user = $request->getAttribute('user');

        if (!$filename || !$folderName) {
            throw new \Exception(
                !$filename
                ?
                'filename not specified or invalid'
                :
                'folder_name not specified',
                400
            );
        }

        $fileExists = checkUserFileExists($user->username, $folderName, $filename);
        $folderExists = userDirExists($user->username, $folderName);
        // got folder as folders() (relationship method returns query builder not as relationship property which returns collection)
        $isUserFolder = $user->folders()->where('folder_name', $folderName)->exists();

        if ($fileExists || !$folderExists || !$isUserFolder) {
            throw new \Exception(
                $fileExists
                ?
                "File \"{$filename}\" already exists"
                :
                "Folder {$folderName} does not exist or invalid filename",
                400
            );
        }

        $this->fileService->createUserFile($user->username, trim($filename), $folderName);

        return ResponseFactory::json(
            ['message' => "File {$filename} created."]
        );
    }

    public function uploadFile(Request $request, Response $response)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $folderName = array_get($request->getParsedBody() ?? [], 'folder_name');
        $user = $request->getAttribute('user');

        if (!$folderName || count($uploadedFiles) === 0) {
            throw new \Exception(
                !$folderName
                ?
                'folder_name not specified'
                :
                'No files were uploaded',
                400
            );
        }

        // got folder as folders() (relationship method returns query builder not as relationship property which returns collection)
        $isUserFolder = $user->folders()->where('folder_name', $folderName)->exists();

        if (!userDirExists($user->username, $folderName) || !$isUserFolder) {
            throw new \Exception("{$folderName} folder does not exist! Create it first.");
        }

        // uploading each file from request and saving its name returned from fileService::saveUploadFile to filenames arr
        foreach ($uploadedFiles as $file) {
            $filenames[] = $this->fileService->saveUploadedFile($user->username, $folderName, $file);
        }

        $filenamesStr = implode(', ', $filenames);
        return ResponseFactory::json([
            "{$filenamesStr} were successfully uploaded to {$folderName}/{$user->username}"
        ]);
    }
}
