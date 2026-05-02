<?php

namespace App\Events;

use App\Models\BusinessAccount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BusinessAccountRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(public BusinessAccount $businessAccount) {}
}
