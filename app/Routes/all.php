<?php

// no need to import these controllers because all folder/files will be handled by composer because we added autoload for App\
use App\Controllers\AuthController;
use App\Controllers\DirController;
use App\Controllers\HomeController;
use App\Middlewares\AuthMiddleware;
use Slim\App;

// returning closure
return function (App $app) {
    $app
        ->get('/', [HomeController::class, 'index'])
        ->add(AuthMiddleware::class);  // creates callable for index on HomeController
    $app
        ->get('/me', [DirController::class, 'createDir'])
        ->add(AuthMiddleware::class);
    // * Auth Related Routes
    $app->post('/register', [AuthController::class, 'register']);
    $app->post('/login', [AuthController::class, 'login']);
    $app->post('/refresh', [AuthController::class, 'refresh']);
    $app->post('/logout', [AuthController::class, 'logout']);
};
