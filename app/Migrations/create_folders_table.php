<?php

// NOTE: This didn't work because migrations are loaded via command line and
// composer does not autoload helpers.php
// require base_path('bootstrap/php');

$basePath = dirname(__DIR__, 2);
require "$basePath/bootstrap.php";

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('folders', function ($table) {
    $table->increments('id');
    $table->string('folder_name');
    $table->foreignId('user_id')->references('id')->on('users');
    $table->timestamps();
});

echo 'Migration Completed';
