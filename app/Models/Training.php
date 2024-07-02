<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'Training',
        'Email',
        'Name',
        'Certification',
    ];

    public static function create($training,$id)
    {
    }
}
