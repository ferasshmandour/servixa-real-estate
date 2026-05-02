<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'locale',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(AdminDeviceToken::class);
    }

    /**
     * Custom routing for the FcmChannel.
     * Returns all active device tokens for this admin.
     */
    public function routeNotificationForFcm($notification = null): array
    {
        return $this->deviceTokens()->pluck('token')->all();
    }
}
