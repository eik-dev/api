<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'folder',
        'title',
        'name',
        'url'
    ];

    public static function create($record)
    {
        $file = new Files();
        $file->user_id = $record['user_id'];
        $file->folder = $record['folder'];
        $file->title = $record['title'];
        $file->name = $record['name'];
        $file->url = $record['url'];
        $file->save();
        return $file;
    }
}
