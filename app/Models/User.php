<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'country',
        'locale',
        'password',
        'is_verified',
        'profile_image',
        'device_token',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Find user by phone for Passport password grant.
     */
    public function findForPassport(string $username): ?self
    {
        return $this->where('phone', $username)->first();
    }

    public function businessAccounts(): HasMany
    {
        return $this->hasMany(BusinessAccount::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function initiatedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'initiator_id');
    }

    public function receivedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'receiver_id');
    }
}
