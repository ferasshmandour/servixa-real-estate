<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class DynamicField extends Model
{
    use HasTranslations;

    protected $fillable = [
        'category_id',
        'label',
        'field_type',
        'options',
        'is_required',
        'sort_order',
    ];

    public $translatable = ['label'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
