<?php

namespace App\Controllers;

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

        if ($fileExists || !$folderExists) {
            throw new \Exception(
                $fileExists
                ?
                "File \"{$filename}\" already exists"
                :
                "Folder {$folderName} does not exist or invalid filename",
                400
            );
        }

        FileService::createUserFile($user->username, trim($filename), $folderName);

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

        if (!userDirExists($user->username, $folderName)) {
            throw new \Exception("{$folderName} does not exist! Create it first.");
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
