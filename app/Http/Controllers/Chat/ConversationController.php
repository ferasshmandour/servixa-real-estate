<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Requests\Chat\StartConversationRequest;
use App\Http\Resources\MessageResource;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ConversationController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function index(Request $request): View
    {
        $conversations = $this->chatService->listConversations($request->user());

        return view('chat.conversations.index', [
            'conversations' => $conversations,
            'currentUserId' => $request->user()->id,
        ]);
    }

    public function start(StartConversationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $conversation = $this->chatService->startConversation(
                (int) $data['service_id'],
                $request->user(),
                isset($data['initiator_business_account_id'])
                    ? (int) $data['initiator_business_account_id']
                    : null,
            );
        } catch (HttpException $e) {
            return back()->with('chat_error', $e->getMessage() ?: __('chat.start_failed'));
        }

        return redirect()->route('chat.conversations.show', $conversation->id);
    }

    public function show(Request $request, int $id): View
    {
        $conversation = $this->chatService->getConversation($id, $request->user());

        // Latest-first paginator; reverse for chronological (oldest→newest) display.
        $messages = $this->chatService->listMessages($id, $request->user());

        $this->chatService->markConversationAsRead($id, $request->user());

        return view('chat.conversations.show', [
            'conversation'  => $conversation,
            'messages'      => $messages,
            'currentUserId' => $request->user()->id,
        ]);
    }

    public function messages(Request $request, int $id): JsonResponse
    {
        $messages = $this->chatService->listMessages($id, $request->user());

        return MessageResource::collection($messages)->response();
    }

    public function send(SendMessageRequest $request, int $id): JsonResponse
    {
        $message = $this->chatService->sendMessage(
            $id,
            $request->validated()['content'],
            $request->user(),
        );

        return (new MessageResource($message))->response()->setStatusCode(201);
    }

    public function read(Request $request, int $id): JsonResponse
    {
        $count = $this->chatService->markConversationAsRead($id, $request->user());

        return response()->json(['marked_as_read' => $count]);
    }
}
