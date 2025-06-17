<?php

// NOTE: This didn't work because migrations are loaded via command line and
// composer does not autoload helpers.php
// require base_path('bootstrap/php');

$basePath = dirname(__DIR__, 2);
require "$basePath/bootstrap.php";

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('files', function ($table) {
    $table->id();  // same as bigIncrements()
    $table->string('filename');
    $table->string('file_ext');
    $table->integer('file_size');
    // since folder_id is convetion can use this way with constrained otherwise needed to do: $table->foreignId('user_id')->references('id')->on('users')
    $table->foreignId('folder_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});

echo 'Migration Completed';
