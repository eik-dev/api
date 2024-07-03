<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;
    protected $fillable = [
        'Training',
        'Email',
        'Name',
        'Number',
    ];

    public static function create($payload)
    {
        $certificate = new Training();
        $certificate->Training = $payload['Training'];
        $certificate->Email = $payload['Email'];
        $certificate->Name = $payload['Name'];
        $certificate->Number = $payload['Number'];
        $certificate->save();
    }
}
