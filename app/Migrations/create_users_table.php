<?php

// NOTE: This didn't work because migrations are loaded via command line and
// composer does not autoload helpers.php
// require base_path('bootstrap/php');
// require base_path('bootstrap/php');

$basePath = dirname(__DIR__, 2);
require "$basePath/bootstrap.php";

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('users', function ($table) {
    $table->increments('id');
    $table->string('email')->unique();
    $table->string('username')->unique();
    $table->string('password');
    $table->string('user_folder_name')->unique();
    $table->string('user_folder_path');
    $table->timestamps();
});

echo 'Migration completed';
