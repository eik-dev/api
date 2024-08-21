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
        'Duties',
        'Email',
        'Phone',
        'start',
        'end'
    ];

    public static function create($professions,$id)
    {
        for ($i=0; $i < count($professions); $i++) { 
            if ( !isset($professions[$i]['Organization']) ||
                !isset($professions[$i]['Location']) ||
                !isset($professions[$i]['Position'])
            ) continue;
            $profession = new Profession();
            $profession->user_id = $id;
            $profession->Organization = $professions[$i]['Organization'];
            $profession->Location = $professions[$i]['Location'];
            $profession->Position = $professions[$i]['Position'];
            $profession->Duties = $professions[$i]['Duties'];
            $profession->Email = $professions[$i]['Email'];
            $profession->Phone = $professions[$i]['Phone'];
            $profession->start = $professions[$i]['start'];
            $profession->end = $professions[$i]['end'];
            $profession->save();
        }
    }
}
