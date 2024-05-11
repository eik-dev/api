<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificates extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'number',
        'expiry',
        'request',
        'verified'
    ];

    public static function create($number,$id)
    {
        $cert = new Certificates();
        $cert->user_id = $id;
        $cert->number = $number;
        $cert->save();
        return $cert;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function individual()
    {
        return $this->belongsTo(Individual::class);
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }
}
