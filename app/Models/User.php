<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Laravel\Passport\HasApiTokens as PassportHasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SanctumHasApiTokens, PassportHasApiTokens {
        // Resolve all trait conflicts
        SanctumHasApiTokens::tokens insteadof PassportHasApiTokens;
        SanctumHasApiTokens::tokenCan insteadof PassportHasApiTokens;
        SanctumHasApiTokens::createToken insteadof PassportHasApiTokens;
        SanctumHasApiTokens::currentAccessToken insteadof PassportHasApiTokens;
        SanctumHasApiTokens::withAccessToken insteadof PassportHasApiTokens;
        
        // Alias Passport methods for OAuth use
        PassportHasApiTokens::tokens as oauthTokens;
        PassportHasApiTokens::tokenCan as oauthTokenCan;
        PassportHasApiTokens::createToken as createOAuthToken;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'nema',
        'number',
        'practicing'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function certificates()
    {
        return $this->hasOne(Certificates::class);
    }

    public function firm()
    {
        return $this->hasOne(Firm::class);
    }

    public function individual()
    {
        return $this->hasOne(Individual::class);
    }

    public function agm(): HasOne
    {
        return $this->hasOne(AGM::class);
    }
}
