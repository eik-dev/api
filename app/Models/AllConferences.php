<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AllConferences extends Model
{
    use HasFactory;
    protected $fillable = [
        'Name',
        'StartDate',
        'EndDate',
    ];
    public function roles(): HasMany
    {
        return $this->hasMany(ConferenceRoles::class, 'conference_id');
    }
}
