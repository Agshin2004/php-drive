<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'folder_name',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
