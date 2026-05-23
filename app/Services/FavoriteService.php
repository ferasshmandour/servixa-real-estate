<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteService
{
    public function listForUser(User $user): LengthAwarePaginator
    {
        return $user->favorites()
            ->with(['service.businessAccount', 'service.category', 'service.subcategory', 'service.media'])
            ->latest()
            ->paginate(15);
    }

    public function add(User $user, int $serviceId): Favorite
    {
        $service = Service::findOrFail($serviceId);

        abort_if($service->status !== 'approved', 422, 'You can only favorite approved services.');

        return Favorite::firstOrCreate([
            'user_id'    => $user->id,
            'service_id' => $service->id,
        ]);
    }

    public function remove(User $user, int $serviceId): void
    {
        $user->favorites()
            ->where('service_id', $serviceId)
            ->delete();
    }
}
