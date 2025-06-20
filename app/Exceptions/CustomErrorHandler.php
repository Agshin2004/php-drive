<?php

namespace App\Exceptions;

use Throwable;
use Slim\Psr7\Response;
use App\Services\LoggerService;
use App\Services\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;

class CustomErrorHandler
{
    public function __construct(private LoggerService $loggerService)
    {
    }

    /**
     * Since classes that have __invoke method can be called as functions (meaning they are callables)
     * and setDefaultErrorHandler expects a callable I grouped all logic inside __invoke
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Throwable $throwable
     * @return \Slim\Psr7\Response
     */
    public function __invoke(ServerRequestInterface $request, Throwable $throwable): Response
    {
        $this->loggerService->error('EXCEPTION >>> ' . $throwable->getMessage());

        return ResponseFactory::json([
            'error' => $throwable->getMessage()
        ], $throwable->getCode() ?: 500);
    }
}
