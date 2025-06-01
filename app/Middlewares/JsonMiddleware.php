<?php

namespace App\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

/**
 * Class that intercepts responses
 * TODO: Implement
 */
class JsonMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): ResponseInterface {}
}
