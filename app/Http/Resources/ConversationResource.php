<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserId = $request->user()?->id;

        $otherParty = $this->initiator_id === $currentUserId
            ? $this->receiver
            : $this->initiator;

        $lastMessage = $this->relationLoaded('messages')
            ? $this->messages->first()
            : null;

        return [
            'id'           => $this->id,
            'service'      => new ServiceResource($this->whenLoaded('service')),
            'other_party'  => $otherParty ? [
                'id'            => $otherParty->id,
                'name'          => trim(($otherParty->first_name ?? '') . ' ' . ($otherParty->last_name ?? '')),
                'profile_image' => $otherParty->profile_image,
            ] : null,
            'last_message' => $lastMessage ? [
                'content'    => $lastMessage->content,
                'sender_id'  => $lastMessage->sender_id,
                'created_at' => $lastMessage->created_at->toIso8601String(),
            ] : null,
            'unread_count' => $this->when(
                $currentUserId !== null,
                fn() => $this->messages()
                    ->where('sender_id', '!=', $currentUserId)
                    ->whereNull('read_at')
                    ->count()
            ),
            'created_at'   => $this->created_at->toIso8601String(),
            'updated_at'   => $this->updated_at->toIso8601String(),
        ];
    }
}
