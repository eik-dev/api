<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConferenceRoles extends Model
{
    use HasFactory;
    protected $fillable = [
        'conference_id',
        'Name',
        'Background',
        'Style',
        'Info',
    ];
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }
}
