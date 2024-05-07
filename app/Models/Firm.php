<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
        'name',
        'alternate',
        'nationality',
        'postal',
        'town',
        'county',
        'nema',
        'kra',
        'phone',
        'bio'
    ];
}
