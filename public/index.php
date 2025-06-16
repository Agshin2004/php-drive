<?php

// use App\Container;
use Slim\Psr7\Response;
use Slim\Factory\AppFactory;
use App\Services\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use DI\ContainerBuilder;

use function DI\autowire;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

$builder = new ContainerBuilder(); // using builder to customize containerbefore using it
$builder->addDefinitions([
    // since PHP-DI container DOES NOT autowire scalars we gotta make use of constructorParameter() method
    \App\Services\FileService::class => autowire()->constructorParameter('uploadDir', base_path('user_store')),
]);
$container = $builder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

// $container = $app->getContainer();
// $container['upload_dir'] = base_path('upload');
// Container::set($container);  // singleton container for all app

// registering body parser middleware
$app->addBodyParsingMiddleware();

// loading all routes
(require __DIR__ . '/../app/Routes/all.php')($app);

// $container = $app->getContainer();
// (require '../app/container.php')($container);

$app->addRoutingMiddleware();

// global error handler
$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
): Response {
    return ResponseFactory::json([
        'error' => $exception->getMessage()
    ], $exception->getCode() ?: 500);
};

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

$app->run();

// url to refresh memory
$app->get('/json', function (Request $request, Response $response, $args): Response {
    $data = ['name' => 'agshin', 'surname' => 'nadirov'];
    $payload = json_encode($data);

    // create a new stream with the payload
    $streamFactory = new StreamFactory();
    // $jsonStream = $streamFactory->createStream($payload);

    // replace the response body and return it
    // $newFileStream = $streamFactory->createStreamFromFile('stream.txt');
    // $response = $response->withBody($newFileStream);
    // closing streams (will cause issues because stream must be open until response is sent)
    // $newFileStream->close();
    // $jsonStream->close();
    // $response = $response->withHeader('Content-Type', 'application/json');

    // Buffers; Downloading file
    // $stream = $streamFactory->createStreamFromFile('large_file.txt', 'r');
    // while (!$stream->eof()) {
    // $buffer = $stream->read(4096);  // buffer size; read up to $length bytes from the object and return them

    // note: cannot pass buffer to withBody because it expects stream and buffers it behind the scenes
    // $response = $response
    //     ->withBody($stream)
    //     ->withHeader('Content-Type', 'application/octet-stream')
    //     ->withHeader('Content-Disposition', 'attachment; filename="large_file.txt"');
    // }

    return $response;
});
