<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HomeController
{
    public function index(Request $request, Response $response): Response
    {
        // $response->getBody()->write(implode(', ', $_ENV));
        $response->getBody()->write('Welcome to home page');
        return $response;
    }
}
