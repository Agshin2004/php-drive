<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'email',
        'username',
        'user_folder_name',
        'user_folder_path',
        'password'
    ];

    protected $hidden = [
        'id',
        'password'
    ];
}
