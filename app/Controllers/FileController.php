<?php

namespace App\Controllers;

use App\Services\FileService;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FileController
{
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
}
