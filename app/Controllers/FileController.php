<?php

namespace App\Controllers;

use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FileController
{
    public function createFile(Request $request, Response $response)
    {
        $filename = array_get($request->getParsedBody(), key: 'filename');
        $folderName = array_get($request->getParsedBody(), 'folder_name');
        $user = $request->getAttribute('user');

        if (!isset($filename) || !isset($folderName)) {
            return ResponseFactory::error('filename or folderName is not specified');
        }

        $fileExists = checkUserFileExists($user->username, $folderName, $filename);
        $folderExists = userDirExists($user->username, $folderName);
        if ($fileExists || !$folderExists) {
            return ResponseFactory::error(
                $fileExists ? "File \"{$filename}\" already exists" : "Folder {$folderName} does not exist"
            );
        }

        $created = createUserFile($user->username, $filename, $folderName);
        if (!$created) {
            return ResponseFactory::error('Something when wrong when creating file', 500);
        }

        return ResponseFactory::json(
            ['message' => "File {$filename} created."]
        );
    }
}
