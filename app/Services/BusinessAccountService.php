<?php

namespace App\Services;

use App\Events\BusinessAccountApproved;
use App\Events\BusinessAccountRejected;
use App\Events\BusinessAccountSubmitted;
use App\Models\BusinessAccount;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BusinessAccountService
{
    // ─── Admin ────────────────────────────────────────────────────────────────

    public function listForAdmin(?string $status = null, ?string $search = null): LengthAwarePaginator
    {
        return BusinessAccount::with(['user', 'city', 'activityType'])
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->when($search, fn($q) => $q->where('license_number', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15);
    }

    public function approve(BusinessAccount $account): void
    {
        abort_if($account->status === 'approved', 422, 'This account is already approved.');

        $account->update([
            'status'           => 'approved',
            'rejection_reason' => null,
        ]);

        BusinessAccountApproved::dispatch($account->fresh());
    }

    public function reject(BusinessAccount $account, string $reason): void
    {
        $account->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        BusinessAccountRejected::dispatch($account->fresh());
    }

    // ─── API ──────────────────────────────────────────────────────────────────

    public function listForUser(User $user): LengthAwarePaginator
    {
        return $user->businessAccounts()
            ->with(['city', 'activityType'])
            ->latest()
            ->paginate(10);
    }

    public function create(User $user, array $data): BusinessAccount
    {
        $account = new BusinessAccount();
        $account->user_id           = $user->id;
        $account->activity_type_id  = $data['activity_type_id'];
        $account->city_id           = $data['city_id'];
        $account->license_number    = $data['license_number'];
        $account->setTranslation('name', 'ar', $data['name_ar'])
                  ->setTranslation('name', 'en', $data['name_en']);
        $account->setTranslation('activities', 'ar', $data['activities_ar'])
                  ->setTranslation('activities', 'en', $data['activities_en']);
        $account->setTranslation('details', 'ar', $data['details_ar'])
                  ->setTranslation('details', 'en', $data['details_en']);
        $account->address   = $data['address'] ?? null;
        $account->latitude  = $data['latitude'] ?? null;
        $account->longitude = $data['longitude'] ?? null;
        $account->status    = 'pending';
        $account->save();

        if (!empty($data['files'])) {
            $this->storeFiles($account, $data['files']);
        }

        BusinessAccountSubmitted::dispatch($account);

        return $account->load(['city', 'activityType']);
    }

    public function update(User $user, BusinessAccount $account, array $data): BusinessAccount
    {
        abort_if($account->user_id !== $user->id, 403, 'You do not own this business account.');
        abort_if($account->status === 'approved', 422, 'Approved accounts cannot be edited.');

        $account->activity_type_id = $data['activity_type_id'] ?? $account->activity_type_id;
        $account->city_id          = $data['city_id'] ?? $account->city_id;
        $account->license_number   = $data['license_number'] ?? $account->license_number;

        if (isset($data['name_ar'])) {
            $account->setTranslation('name', 'ar', $data['name_ar']);
        }
        if (isset($data['name_en'])) {
            $account->setTranslation('name', 'en', $data['name_en']);
        }
        if (isset($data['activities_ar'])) {
            $account->setTranslation('activities', 'ar', $data['activities_ar']);
        }
        if (isset($data['activities_en'])) {
            $account->setTranslation('activities', 'en', $data['activities_en']);
        }
        if (isset($data['details_ar'])) {
            $account->setTranslation('details', 'ar', $data['details_ar']);
        }
        if (isset($data['details_en'])) {
            $account->setTranslation('details', 'en', $data['details_en']);
        }

        $account->address   = $data['address'] ?? $account->address;
        $account->latitude  = $data['latitude'] ?? $account->latitude;
        $account->longitude = $data['longitude'] ?? $account->longitude;
        $account->save();

        // Append new files (images → 'images' collection, documents → 'documents' collection)
        if (!empty($data['files'])) {
            $this->storeFiles($account, $data['files']);
        }

        return $account->load(['city', 'activityType']);
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function storeFiles(BusinessAccount $account, array $files): void
    {
        foreach ($files as $file) {
            $collection = str_contains($file->getMimeType(), 'image') ? 'images' : 'documents';

            $account->addMedia($file)
                    ->toMediaCollection($collection);
        }
    }
}
