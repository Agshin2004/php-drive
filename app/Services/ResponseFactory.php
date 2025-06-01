<?php

namespace App\Services;

use Slim\Psr7\Response;  // used slim's implementation of Psr7 because we will need instance of Response

// NOTE: I know I could use laravel's ResponseFactory but decided to write my own
class ResponseFactory
{
    public static function json(array $data, int $status = 200): Response
    {
        $response = new Response();
        $payload = [
            'success' => $status !== 200 ? false : true,
            'payload' => $data,
        ];
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
