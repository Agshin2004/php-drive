<?php

namespace App\Models;

use App\Models\Folder;
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

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }
}
