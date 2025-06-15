<?php

namespace App\Services;

// used slim's implementation of Psr7 because we will need instance of (CONCRETE) Response
use Slim\Psr7\Response;  // since we CANNOT instantiate interface (slim passed implementation behind the scenes), we gotta make use of CONCRETE Response

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

    public static function error(string $message, int $status = 400): Response
    {
        $response = new Response();
        $payload = [
            'success' => false,
            'payload' => $message
        ];

        $response->getBody()->write(json_encode($payload));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
