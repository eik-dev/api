<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conference extends Model
{
    use HasFactory;
    protected $fillable = [
        'conference_id',
        'role_id',
        'Name',
        'Email',
        'Number',
        'Sent',
    ];

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
    public function role(): BelongsTo
    {
        return $this->belongsTo(ConferenceRoles::class);
    }
}
