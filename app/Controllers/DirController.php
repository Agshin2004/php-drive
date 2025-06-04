<?php

namespace App\Controllers;

use App\Models\Folder;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DirController
{
    public function getUserDirs(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        $folderNames = array_map(fn($folder) => $folder['folder_name'], $user->folders->toArray());

        return ResponseFactory::json([
            $folderNames
        ]);
    }

    public function createDir(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        $dirName = $request->getParsedBody()['dirName'];

        if (!isset($dirName) || !is_string($dirName) || trim($dirName) === '') {
            throw new \Exception('Invalid directory name', 422);  // could use 400 status code but decided to use 422
        }

        // check if directory with new dir name does not exist
        if (userDirExists($user->username, $dirName)) {
            throw new \Exception("{$dirName} directory already exists.", 400);
        }

        // create directory for user
        createUserDir($user->username, $dirName);

        // add foldername to db
        Folder::create([
            'user_id' => $user->id,
            'folder_name' => $dirName,
        ]);

        return ResponseFactory::json(['message' => "{$dirName} created"], 201);
    }

    public function createFile(Request $request, Response $response) 
    {
        // check file for viruses!
        
    }
}
