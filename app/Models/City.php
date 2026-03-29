<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    public function businessAccounts(): HasMany
    {
        return $this->hasMany(BusinessAccount::class);
    }
}
