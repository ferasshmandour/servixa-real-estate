<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Rating\StoreRatingRequest;
use App\Http\Resources\RatingResource;
use App\Services\RatingService;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    public function __construct(private RatingService $ratingService) {}

    public function store(StoreRatingRequest $request): JsonResponse
    {
        $rating = $this->ratingService->createRating($request->validated(), $request->user());

        return $this->success(new RatingResource($rating), 'Rating submitted successfully.', 201);
    }
}
