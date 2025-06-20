<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MiscController
{
    public function search(Request $request, Response $response)
    {
        $query = array_get($request->getQueryParams(), 'query');
        if (!$query) {
            throw new \Exception('No query param', 400);
        }

        // LIKE: % = any chars, _ = one char ('%abc%' contains, 'abc%' starts with, '%abc' ends with, 'a_c' = 'abc')
        $files = File::where('filename', 'LIKE', '%' . strtolower($query) . '%')->get()->toArray();
        $dirs = Folder::where('folder_name', 'LIKE', '%' . strtolower($query) . '%')->get()->toArray();

        $jsonArr = [
            'folder_count' => count($files),
            'files_count' => count($dirs),
        ];

        if (!empty($files)) {
            $jsonArr['files'] = $files;
        }

        if (!empty($dirs)) {
            $jsonArr['folders'] = $dirs;
        }

        return ResponseFactory::json($jsonArr);
    }
}
