<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public function startConversation(int $serviceId, User $user, ?int $businessAccountId = null): Conversation
    {
        $service = Service::with('businessAccount')->findOrFail($serviceId);

        $receiverId = $service->businessAccount?->user_id;

        // The receiver is always the service owner — who, by definition, holds an
        // approved business account. This makes the "business account" side always
        // present, so user↔user conversations are structurally impossible.
        abort_if(
            $receiverId === null,
            422,
            'Service has no owner.'
        );

        abort_if(
            $receiverId === $user->id,
            422,
            'You cannot start a conversation about your own service.'
        );

        // Optional "wallet": the initiator may act as one of their own approved
        // business accounts (business↔business) instead of as a plain user.
        if ($businessAccountId !== null) {
            $businessAccount = $user->businessAccounts()->find($businessAccountId);

            abort_if(
                $businessAccount === null,
                403,
                'That business account does not belong to you.'
            );

            abort_if(
                $businessAccount->status !== 'approved',
                422,
                'You can only chat through an approved business account.'
            );
        }

        $conversation = Conversation::firstOrCreate(
            [
                'service_id'   => $service->id,
                'initiator_id' => $user->id,
                'receiver_id'  => $receiverId,
            ],
            [
                // Recorded once, on first creation only.
                'initiator_business_account_id' => $businessAccountId,
            ]
        );

        return $conversation->load([
            'service.businessAccount',
            'initiator',
            'initiatorBusinessAccount',
            'receiver',
        ]);
    }

    public function listConversations(User $user): LengthAwarePaginator
    {
        return Conversation::with([
                'service.businessAccount',
                'initiator',
                'initiatorBusinessAccount',
                'receiver',
                'messages' => fn($q) => $q->latest()->limit(1),
            ])
            ->where('initiator_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->latest('updated_at')
            ->paginate(20);
    }

    public function getConversation(int $conversationId, User $user): Conversation
    {
        $conversation = Conversation::with([
            'service.businessAccount',
            'initiator',
            'initiatorBusinessAccount',
            'receiver',
        ])->findOrFail($conversationId);

        $this->authorizeParticipant($conversation, $user);

        return $conversation;
    }

    public function listMessages(int $conversationId, User $user): LengthAwarePaginator
    {
        $conversation = Conversation::findOrFail($conversationId);
        $this->authorizeParticipant($conversation, $user);

        return $conversation->messages()
            ->with('sender')
            ->latest()
            ->paginate(30);
    }

    public function sendMessage(int $conversationId, string $content, User $user): Message
    {
        $conversation = Conversation::findOrFail($conversationId);
        $this->authorizeParticipant($conversation, $user);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $content,
            'status'          => 'sent',
        ]);

        $conversation->touch();

        MessageSent::dispatch($message);

        return $message->load('sender');
    }

    public function markConversationAsRead(int $conversationId, User $user): int
    {
        $conversation = Conversation::findOrFail($conversationId);
        $this->authorizeParticipant($conversation, $user);

        return $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
    }

    private function authorizeParticipant(Conversation $conversation, User $user): void
    {
        abort_if(
            $conversation->initiator_id !== $user->id && $conversation->receiver_id !== $user->id,
            403,
            'You are not a participant in this conversation.'
        );
    }
}
