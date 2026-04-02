<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class BusinessAccount extends Model
{
    use HasTranslations;

    protected $fillable = [
        'user_id',
        'activity_type_id',
        'city_id',
        'license_number',
        'name',
        'activities',
        'details',
        'address',
        'latitude',
        'longitude',
        'status',
        'rejection_reason',
    ];

    public $translatable = ['name', 'activities', 'details'];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(BusinessAccountFile::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'requester_business_id');
    }
}
