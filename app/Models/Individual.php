<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    use HasFactory;
    protected $fillable = [
        'category',
        'firm',
        'alternate',
        'nationality',
        'nationalID',
        'postal',
        'town',
        'county',
        'kra',
        'phone',
        'bio'
    ];

    public static function create($profile,$id)
    {
        $individual = new Individual();
        $individual->user_id = $id;
        $individual->category = $profile['category'];
        $individual->firm = $profile['firm'];
        $individual->alternate = $profile['alternate'];
        $individual->nationality = $profile['nationality'];
        $individual->nationalID = $profile['nationalID'];
        $individual->postal = $profile['postal'];
        $individual->town = $profile['town'];
        $individual->county = $profile['county'];
        $individual->kra = $profile['kra'];
        $individual->phone = $profile['phone'];
        $individual->bio = $profile['note'];
        $individual->save();
        return $individual;
    }

    public function certificates()
    {
        return $this->hasOne(Certificates::class);
    }
}
