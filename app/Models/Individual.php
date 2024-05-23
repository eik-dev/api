<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profession;
use App\Models\Education;

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
        'phone',
        'bio'
    ];

    public static function create($profile,$id, $education, $profession)
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
        $individual->phone = $profile['phone'];
        $individual->bio = $profile['note'];
        $individual->save();
        if (count($education)>0)   Education::create($education,$id);
        if (count($profession)>0)  Profession::create($profession,$id);
        return $individual;
    }

    public function certificates()
    {
        return $this->hasOne(Certificates::class);
    }
}
