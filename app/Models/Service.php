<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'business_account_id',
        'category_id',
        'subcategory_id',
        'title',
        'description',
        'available_quantity',
        'type',
        'price_syp',
        'price_usd',
        'latitude',
        'longitude',
        'status',
        'rejection_reason',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main-image')->singleFile();
        $this->addMediaCollection('additional-images');
    }

    public $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return [
            'price_syp' => 'decimal:2',
            'price_usd' => 'decimal:2',
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function businessAccount(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function dynamicValues(): HasMany
    {
        return $this->hasMany(ServiceDynamicValue::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
