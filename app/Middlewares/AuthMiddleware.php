<?php

namespace App\Middlewares;

use App\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * process incoming server request
     * @return ResponseInterface
     */
    public function process(Request $request, Handler $handler): ResponseInterface
    {
        $authHeader = $request->getHeader('authorization')[0] ?? null;
        if (!$authHeader) {
            return ResponseFactory::json(['error' => 'No JWT token provided'], 403);
        }

        $jwt = explode(' ', $authHeader)[1];

        if (!validateJwt($jwt)) {
            return ResponseFactory::json(['error' => 'invalid JWT']);
        }

        $user = getUserFromJwt($jwt);
        // NOTE: withAttribute does not change $request it returns new $request with that attribute attached to it
        $request = $request->withAttribute('user', $user);

        // pass request to the next middleware in the stack and get the response returned by the next middleware or the final route/controller
        $response = $handler->handle($request);
        // $response->getBody()->write("\n[AuthMiddleware executed]");  // cheatsheet for myself: can add something to response

        return $response;
    }
}
