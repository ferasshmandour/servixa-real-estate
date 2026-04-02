<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ActivityType extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
    ];

    public $translatable = ['name'];

    public function businessAccounts(): HasMany
    {
        return $this->hasMany(BusinessAccount::class);
    }
}
