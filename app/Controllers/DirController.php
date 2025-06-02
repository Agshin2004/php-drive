<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DirController
{
    public function createDir(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        dd($user);
    }
}
