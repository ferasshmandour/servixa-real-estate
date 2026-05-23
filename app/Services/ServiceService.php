<?php

namespace App\Services;

use App\Events\ServiceApproved;
use App\Events\ServiceRejected;
use App\Events\ServiceResubmitted;
use App\Events\ServiceSubmitted;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceService
{
    // ─── Admin ────────────────────────────────────────────────────────────────

    public function listForAdmin(?string $status = null, ?string $search = null): LengthAwarePaginator
    {
        return Service::with(['businessAccount', 'category', 'subcategory', 'media'])
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->whereRaw("JSON_SEARCH(LOWER(title), 'one', LOWER(?)) IS NOT NULL", ["%{$search}%"]))
            ->latest()
            ->paginate(15);
    }

    public function approve(Service $service): void
    {
        abort_if($service->status === 'approved', 422, 'This service is already approved.');

        $service->update([
            'status'           => 'approved',
            'rejection_reason' => null,
        ]);

        ServiceApproved::dispatch($service->fresh());
    }

    public function reject(Service $service, string $reason): void
    {
        $service->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        ServiceRejected::dispatch($service->fresh());
    }

    // ─── API — Public ─────────────────────────────────────────────────────────

    public function listPublic(array $filters): LengthAwarePaginator
    {
        $query = Service::with(['businessAccount', 'category', 'subcategory', 'media'])
            ->where('status', 'approved')
            ->when(
                isset($filters['category_id']),
                fn($q) => $q->where('category_id', $filters['category_id'])
            )
            ->when(
                isset($filters['subcategory_id']),
                fn($q) => $q->where('subcategory_id', $filters['subcategory_id'])
            )
            ->when(
                isset($filters['type']),
                fn($q) => $q->where('type', $filters['type'])
            )
            ->when(
                isset($filters['city_id']),
                fn($q) => $q->whereHas('businessAccount', fn($b) => $b->where('city_id', $filters['city_id']))
            )
            ->when(
                isset($filters['activity_type_id']),
                fn($q) => $q->whereHas('businessAccount', fn($b) => $b->where('activity_type_id', $filters['activity_type_id']))
            )
            ->when(
                isset($filters['price_syp_min']),
                fn($q) => $q->where('price_syp', '>=', $filters['price_syp_min'])
            )
            ->when(
                isset($filters['price_syp_max']),
                fn($q) => $q->where('price_syp', '<=', $filters['price_syp_max'])
            )
            ->when(
                isset($filters['price_usd_min']),
                fn($q) => $q->where('price_usd', '>=', $filters['price_usd_min'])
            )
            ->when(
                isset($filters['price_usd_max']),
                fn($q) => $q->where('price_usd', '<=', $filters['price_usd_max'])
            )
            ->when(
                isset($filters['min_rating']),
                fn($q) => $q->withAvg('ratings', 'rating')
                            ->having('ratings_avg_rating', '>=', $filters['min_rating'])
            )
            ->when(
                isset($filters['search']),
                function ($q) use ($filters) {
                    $words = array_filter(explode(' ', $filters['search']));
                    foreach ($words as $word) {
                        $q->whereRaw("JSON_SEARCH(LOWER(title), 'one', LOWER(?)) IS NOT NULL", ["%{$word}%"]);
                    }
                }
            );

        match ($filters['sort_by'] ?? 'newest') {
            'oldest'     => $query->oldest(),
            'price_asc'  => $query->orderByRaw('COALESCE(price_syp, price_usd * 14000) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(price_syp, price_usd * 14000) DESC'),
            default      => $query->latest(),
        };

        return $query->paginate(15);
    }

    // ─── API — User ───────────────────────────────────────────────────────────

    public function listForUser(User $user): LengthAwarePaginator
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');

        return Service::with(['businessAccount', 'category', 'subcategory', 'media'])
            ->whereIn('business_account_id', $businessAccountIds)
            ->latest()
            ->paginate(15);
    }

    public function create(User $user, array $data): Service
    {
        $businessAccount = $user->businessAccounts()->findOrFail($data['business_account_id']);

        abort_if(
            $businessAccount->status !== 'approved',
            422,
            'You can only post services through an approved business account.'
        );

        $service = new Service();
        $service->business_account_id = $businessAccount->id;
        $service->category_id         = $data['category_id'];
        $service->subcategory_id      = $data['subcategory_id'] ?? null;
        $service->setTranslation('title', 'ar', $data['title_ar'])
                 ->setTranslation('title', 'en', $data['title_en']);
        $service->setTranslation('description', 'ar', $data['description_ar'])
                 ->setTranslation('description', 'en', $data['description_en']);
        $service->available_quantity = $data['available_quantity'] ?? 1;
        $service->type               = $data['type'];
        $service->price_syp          = $data['price_syp'] ?? null;
        $service->price_usd          = $data['price_usd'] ?? null;
        $service->latitude           = $data['latitude'] ?? null;
        $service->longitude          = $data['longitude'] ?? null;
        $service->status             = 'pending';
        $service->save();

        // Store main image via Media Library
        $service->addMedia($data['main_image'])
                ->toMediaCollection('main-image');

        // Store additional images
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $service->addMedia($image)
                        ->toMediaCollection('additional-images');
            }
        }

        // Store dynamic field values
        if (!empty($data['dynamic_values'])) {
            $this->storeDynamicValues($service, $data['dynamic_values']);
        }

        ServiceSubmitted::dispatch($service);

        return $service->load(['businessAccount', 'category', 'subcategory', 'dynamicValues.dynamicField']);
    }

    public function update(User $user, Service $service, array $data): Service
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');
        abort_if(
            !$businessAccountIds->contains($service->business_account_id),
            403,
            'You do not own this service.'
        );

        // Auto-reset to pending if editing an approved service
        $wasApprovedAndReset = false;
        if ($service->status === 'approved') {
            $service->status = 'pending';
            $service->rejection_reason = null;
            $wasApprovedAndReset = true;
        }

        if (isset($data['category_id'])) {
            $service->category_id = $data['category_id'];
        }
        if (array_key_exists('subcategory_id', $data)) {
            $service->subcategory_id = $data['subcategory_id'];
        }
        if (isset($data['title_ar'])) {
            $service->setTranslation('title', 'ar', $data['title_ar']);
        }
        if (isset($data['title_en'])) {
            $service->setTranslation('title', 'en', $data['title_en']);
        }
        if (isset($data['description_ar'])) {
            $service->setTranslation('description', 'ar', $data['description_ar']);
        }
        if (isset($data['description_en'])) {
            $service->setTranslation('description', 'en', $data['description_en']);
        }
        if (isset($data['available_quantity'])) {
            $service->available_quantity = $data['available_quantity'];
        }
        if (isset($data['type'])) {
            $service->type = $data['type'];
        }
        if (array_key_exists('price_syp', $data)) {
            $service->price_syp = $data['price_syp'];
        }
        if (array_key_exists('price_usd', $data)) {
            $service->price_usd = $data['price_usd'];
        }
        if (array_key_exists('latitude', $data)) {
            $service->latitude = $data['latitude'];
        }
        if (array_key_exists('longitude', $data)) {
            $service->longitude = $data['longitude'];
        }

        $service->save();

        // Replace main image if provided (singleFile collection auto-deletes old)
        if (isset($data['main_image'])) {
            $service->addMedia($data['main_image'])
                    ->toMediaCollection('main-image');
        }

        // Append new additional images
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $service->addMedia($image)
                        ->toMediaCollection('additional-images');
            }
        }

        // Re-upsert dynamic values
        if (!empty($data['dynamic_values'])) {
            $this->storeDynamicValues($service, $data['dynamic_values']);
        }

        if ($wasApprovedAndReset) {
            ServiceResubmitted::dispatch($service->fresh());
        }

        return $service->load(['businessAccount', 'category', 'subcategory', 'dynamicValues.dynamicField']);
    }

    public function delete(User $user, Service $service): void
    {
        $businessAccountIds = $user->businessAccounts()->pluck('id');
        abort_if(
            !$businessAccountIds->contains($service->business_account_id),
            403,
            'You do not own this service.'
        );

        // Media Library automatically deletes all associated files on model delete
        $service->delete();
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function storeDynamicValues(Service $service, array $values): void
    {
        foreach ($values as $item) {
            $service->dynamicValues()->updateOrCreate(
                ['dynamic_field_id' => $item['field_id']],
                ['value'            => $item['value']]
            );
        }
    }
}
