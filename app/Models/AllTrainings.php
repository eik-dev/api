<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllTrainings extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'Name',
        'StartDate',
        'EndDate',
        'View',
        'Background',
        'Style',
        'Info',
    ];

    public static function create($name, $start, $end, $info){
        $training = new AllTrainings();
        $training->Name = $name;
        $training->StartDate = $start;
        $training->EndDate = $end;
        $training->Info = $info;
        $training->save();
    }
}
