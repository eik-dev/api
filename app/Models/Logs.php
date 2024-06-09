<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'username',
        'email',
        'action'
    ];
    public static function create($payload){
        $log = new Logs();
        $log->user = $payload['name'];
        $log->email = $payload['email'];
        $log->action = $payload['action'];
        $log->save();
    }
}
