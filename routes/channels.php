<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Private channel for a single conversation. The authenticated user (resolved
| from the default `web` guard via /broadcasting/auth) may subscribe only if
| they are one of the two participants — which blocks outsiders and enforces
| the participant rules at the transport layer.
|
*/

Broadcast::channel('conversations.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::find($conversationId);

    if ($conversation === null) {
        return false;
    }

    return $user->id === $conversation->initiator_id
        || $user->id === $conversation->receiver_id;
});
