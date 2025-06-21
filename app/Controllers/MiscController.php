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
        $queryParams = $request->getQueryParams();
        $query = array_get($queryParams, 'query');
        $extFilter = array_get($queryParams, 'ext', null);
        $fileSizeFilter = array_get($queryParams, 'size', null);

        $sizeOperator = '>='; // defaulting filter operator to >= of not specified
        $sizeValue = 0;
        if ($fileSizeFilter) {
            $validOperator = collect(FILTER_OPERATORS)->first(fn($operator) => str_starts_with($fileSizeFilter, $operator));
            if (! $validOperator) {
                throw new \Exception('Unsupported lookup operator');
            }

            foreach (FILTER_OPERATORS as $operator) {
                if (str_starts_with($fileSizeFilter, $operator)) {
                    $sizeOperator = $operator;
                    $sizeValue = substr($fileSizeFilter, strlen($operator));
                    break;
                }
            }

            if (!$sizeValue) {
                $sizeValue = $fileSizeFilter;
                $sizeOperator = '=';
            }
        }
        // dd($sizeOperator, $sizeValue);

        // getting cleint's ip
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
        $files = File::where('filename', 'LIKE', '%' . strtolower($query) . '%')
            ->where('file_ext', 'LIKE', "%{$extFilter}%")
            ->where('file_size', $sizeOperator, $sizeValue)
            ->get()
            ->toArray();

        $dirs = Folder::where('folder_name', 'LIKE', '%' . strtolower($query) . '%')
            ->get()
            ->toArray();

        $this->loggerService->info('Search completed', [
            'query' => $query,
            'files_found' => count($files),
            'folders_found' => count($dirs)
        ]);

        $jsonArr = [
            'files_count' => count($files),
            'folder_count' => count($dirs),
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
