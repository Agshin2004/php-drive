<?php

// no need to import these controllers because all folder/files will be handled by composer because we added autoload for App\
use App\Controllers\AuthController;
use App\Controllers\DirController;
use App\Controllers\FileController;
use App\Controllers\HomeController;
use App\Controllers\MiscController;
use App\Middlewares\AuthMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// returning closure
return function (App $app) {
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group
            ->get('/', [HomeController::class, 'index'])  // creates callable for index on HomeController
            ->add(AuthMiddleware::class);
        $group
            ->get('/search', [MiscController::class, 'search'])
            ->add(AuthMiddleware::class);
        $group
            ->get('/dirs', [DirController::class, 'getUserDirs'])
            ->add(AuthMiddleware::class);
        $group
            ->post('/dirs', [DirController::class, 'createDir'])
            ->add(AuthMiddleware::class);
        $group
            ->get('/files', [FileController::class, 'getUserFiles'])
            ->add(AuthMiddleware::class);
        $group
            ->get('/files/{id}', [FileController::class, 'getUserFile'])
            ->add(AuthMiddleware::class);
        $group
            ->post('/files', [FileController::class, 'createFile'])
            ->add(AuthMiddleware::class);
        $group
            ->post('/upload-file', [FileController::class, 'uploadFile'])
            ->add(AuthMiddleware::class);

        // * Auth Related Routes
        $group->group('/auth', function (RouteCollectorProxy $group) {
            $group->post('/register', [AuthController::class, 'register']);
            $group->post('/login', [AuthController::class, 'login']);
            $group->post('/refresh', [AuthController::class, 'refresh'])->add(AuthMiddleware::class);
            $group->post('/logout', [AuthController::class, 'logout']);
        });
    });
};
