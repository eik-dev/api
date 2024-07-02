<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllTrainings extends Model
{
    use HasFactory;
    protected $fillable = [
        'Name',
        'Certificate',
        'Date',
        'View',
    ];
}
