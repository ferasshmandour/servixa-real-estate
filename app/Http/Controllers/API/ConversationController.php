<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Chat\StartConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function index(Request $request): JsonResponse
    {
        $conversations = $this->chatService->listConversations($request->user());

        return $this->success(ConversationResource::collection($conversations));
    }

    public function store(StartConversationRequest $request): JsonResponse
    {
        $conversation = $this->chatService->startConversation(
            (int) $request->validated()['service_id'],
            $request->user()
        );

        return $this->success(
            new ConversationResource($conversation),
            'Conversation started.',
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $conversation = $this->chatService->getConversation($id, $request->user());

        return $this->success(new ConversationResource($conversation));
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $count = $this->chatService->markConversationAsRead($id, $request->user());

        return $this->success(['marked_as_read' => $count], 'Messages marked as read.');
    }
}
