<?php

namespace App\Controllers;

use App\Models\Folder;
use App\Services\DirService;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DirController
{
    public function getUserDirs(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        $page = array_get($request->getQueryParams(), 'page');
        $perPage = 4;
        $offset = ($page - 1) * $perPage;

        $folders = $user
            ->folders()
            ->offset($offset)
            ->limit($perPage)
            ->get()
            ->toArray();

        $folderNames = array_map(
            fn ($folder) => $folder['folder_name'],
            $folders
        );

        return ResponseFactory::json([
            $folderNames
        ]);
    }

    public function createDir(Request $request, Response $response)
    {
        $user = $request->getAttribute('user'); // NOTE: User gets attached in AuthMiddleware
        $dirName = array_get($request->getParsedBody(), 'dir_name');

        if (!isset($dirName) || !is_string($dirName) || trim($dirName) === '') {
            throw new \Exception('dir_name is not specified or invalid', 422);  // could use 400 status code but decided to use 422
        }

        // check if directory with new dir name does not exist
        if (userDirExists($user->username, $dirName)) {
            throw new \Exception("{$dirName} directory already exists.", 400);
        }

        // create directory for user
        DirService::createDir($user->username, $dirName);

        // add foldername to db
        Folder::create([
            'user_id' => $user->id,
            'folder_name' => $dirName,
        ]);

        return ResponseFactory::json(['message' => "{$dirName} created"], 201);
    }
}
