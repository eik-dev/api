<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Map;

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

    public static function generateNumber($category, $id){
        $number = '';
        //check if id has old_id in model Map
        $map = Map::where('new_id', $id)->first();
        if($map) $id = $map->old_id; //set id from old
        else{ //if id doesn't have a link to an old_id
            $map = new Map();
            $map->new_id = $id;
            $id = Map::max('old_id') + 1;
            $map->old_id = $id;
            $map->save();
        }
        switch ($category) {
            case 'Student':
                $number = 'EIK/5/' . $id;
                break;
            case 'Associate':
                $number = 'EIK/2/' . $id;
                break;
            case 'Fellow':
                $number = 'EIK/7/' . $id;
                break;
            case 'Honorary':
                $number = 'EIK/6/' . $id;
                break;
            case 'Affiliate':
                $number = 'EIK/4/' . $id;
                break;
            case 'Lead':
                $number = 'EIK/1/' . $id;
                break;
            case 'Corporate':
                $number = 'EIK/3/' . $id;
                break;
            case 'Firms':
                $number = 'EIK/3/' . $id;
                break;
            default:
                $number = 'EIK/0/' . $id;
                break;
        }
        return $number;
    }

    public static function create($category,$id)
    {
        $cert = new Certificates();
        $cert->user_id = $id;
        $cert->number = self::generateNumber($category, $id);
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
