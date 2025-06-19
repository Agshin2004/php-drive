<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Database\Eloquent\Collection $folders
 * @method \Illuminate\Database\Eloquent\Relations\HasMany folders()
 */
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
