<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Services\LoggerService;
use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MiscController
{
    public function __construct(private LoggerService $loggerService)
    {
    }

    public function search(Request $request, Response $response)
    {
        $query = array_get($request->getQueryParams(), 'query');

        // getting cleint's i[
        $serverParams = $request->getServerParams();
        $ip = $serverParams['REMOTE_ADDR'] ?? 'unkwown ip';

        if (!$query) {
            $this->loggerService->info('Search attempted without query paramter', [
                'CLIENT UP' => $ip
            ]);
            throw new \Exception('No query param', 400);
        }

        $this->loggerService->info('Started search', [
            'query' => $query,
            'CLEINT IP' => $ip,
        ]);

        // LIKE: % = any chars, _ = one char ('%abc%' contains, 'abc%' starts with, '%abc' ends with, 'a_c' = 'abc')
        $files = File::where('filename', 'LIKE', '%' . strtolower($query) . '%')->get()->toArray();
        $dirs = Folder::where('folder_name', 'LIKE', '%' . strtolower($query) . '%')->get()->toArray();

        $this->loggerService->info('Search completed', [
            'query' => $query,
            'files_found' => count($files),
            'folders_found' => count($dirs)
        ]);

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
