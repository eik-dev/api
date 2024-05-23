<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
        'kra',
        'alternate',
        'nationality',
        'postal',
        'town',
        'county',
        'phone',
        'bio'
    ];

    public static function create($profile,$id)
    {
        $firm = new Firm();
        $firm->user_id = $id;
        $firm->category = $profile['category'];
        $firm->kra = $profile['kra'];
        $firm->kra = $profile['nationality'];
        $firm->alternate = $profile['alternate'];
        $firm->postal = $profile['postal'];
        $firm->town = $profile['town'];
        $firm->county = $profile['county'];
        $firm->phone = $profile['phone'];
        $firm->bio = $profile['note'];
        $firm->save();
        return $firm;
    }

    public function certificates()
    {
        return $this->hasOne(Certificates::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
