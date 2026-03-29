<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'service_id',
        'requester_business_id',
        'needed_at',
        'quantity',
        'details',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'needed_at' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function requesterBusiness(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class, 'requester_business_id');
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }
}
