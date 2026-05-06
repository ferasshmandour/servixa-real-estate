<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Chat\SendMessageRequest;
use App\Http\Resources\MessageResource;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function index(Request $request, int $conversationId): JsonResponse
    {
        $messages = $this->chatService->listMessages($conversationId, $request->user());

        return $this->success(MessageResource::collection($messages));
    }

    public function store(SendMessageRequest $request, int $conversationId): JsonResponse
    {
        $message = $this->chatService->sendMessage(
            $conversationId,
            $request->validated()['content'],
            $request->user()
        );

        return $this->success(new MessageResource($message), 'Message sent.', 201);
    }
}
