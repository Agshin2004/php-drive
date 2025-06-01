<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

// as of now bootstrap.php copntains set up for Eloquent, env

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$capsule = new Capsule();
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database.sqlite',
    'prefix' => ''
]);
$capsule->setAsGlobal();  // make db globally accessible using Capsule
$capsule->bootEloquent(); // booting so models are globally available
