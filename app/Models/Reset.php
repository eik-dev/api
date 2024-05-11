<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reset extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'token'
    ];

    public static function create($email)
    {
        $token = md5($email . time());
        $reset = new Reset();
        $reset->email = $email;
        $reset->token = $token;
        $reset->save();
        return $reset;
    }
}
