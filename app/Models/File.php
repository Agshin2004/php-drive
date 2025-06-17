<?php

namespace App\Models;

use App\Models\Folder;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'filename',
        'file_ext',
        'file_size',
        'folder_id',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}
