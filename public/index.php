<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';

$app = AppFactory::create();

// registering body parser middleware
$app->addBodyParsingMiddleware();

// loading all routes
(require __DIR__ . '/../app/Routes/all.php')($app);


$app->run();






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

