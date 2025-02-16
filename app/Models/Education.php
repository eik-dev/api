<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'Title',
        'Institution',
        'Certification',
        'start',
        'end'
    ];
    public static function create($educations,$id)
    {
        for ($i=0; $i < count($educations); $i++) { 
            if ( !isset($educations[$i]['Title']) ||
                !isset($educations[$i]['institution']) ||
                !isset($educations[$i]['Certification'])
            ) continue;
            $education = new Education();
            $education->user_id = $id;
            $education->Title = $educations[$i]['Title'];
            $education->Institution = $educations[$i]['institution'];
            $education->Certification = $educations[$i]['Certification'];
            $education->start = $educations[$i]['start'];
            $education->end = $educations[$i]['end'];
            $education->save();
        }
    }
}
