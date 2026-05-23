<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Favorite\StoreFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $favoriteService) {}

    public function index(Request $request): JsonResponse
    {
        $favorites = $this->favoriteService->listForUser($request->user());

        return $this->success(FavoriteResource::collection($favorites));
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $favorite = $this->favoriteService->add(
            $request->user(),
            (int) $request->validated()['service_id']
        );

        return $this->success(
            new FavoriteResource($favorite->load('service')),
            'Added to favorites.',
            201
        );
    }

    public function destroy(Request $request, int $service): JsonResponse
    {
        $this->favoriteService->remove($request->user(), $service);

        return $this->success(null, 'Removed from favorites.');
    }
}
