<?php

require_once __DIR__ . '/../bootstrap.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;


$app = AppFactory::create();


$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(implode(', ', $_ENV));
    return $response;
});


// url to refresh memory
$app->get('/json', function (Request $request, Response $response, $args) {
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

$app->run();
