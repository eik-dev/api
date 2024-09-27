<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TWG extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','twgs'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function create($user_id, $twgs)
    {
        $twg = new TWG();
        $twg->user_id = $user_id;
        $twg->twgs = json_encode([$twgs]);
        $twg->save();
        return $twg;
    }
}
