<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'Organization',
        'Location',
        'Position',
        'start',
        'end'
    ];

    public static function create($professions,$id)
    {
        for ($i=0; $i < count($professions); $i++) { 
            $profession = new Profession();
            $profession->user_id = $id;
            $profession->Organization = $professions[$i]['Organization'];
            $profession->Location = $professions[$i]['Location'];
            $profession->Position = $professions[$i]['Position'];
            $profession->start = $professions[$i]['start'];
            $profession->end = $professions[$i]['end'];
            $profession->save();
        }
    }
}
