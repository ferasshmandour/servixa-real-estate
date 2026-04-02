<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations;

    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'sort_order',
    ];

    public $translatable = ['name'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function dynamicFields(): HasMany
    {
        return $this->hasMany(DynamicField::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
