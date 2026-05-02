<?php

namespace App\Listeners;

use App\Events\MessageSent;
use App\Notifications\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMessageSentNotification implements ShouldQueue
{
    public string $queue = 'notifications';
    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;

        if (!$conversation) {
            return;
        }

        // Recipient is the side of the conversation that didn't send the message.
        $recipient = $message->sender_id === $conversation->initiator_id
            ? $conversation->receiver
            : $conversation->initiator;

        if (!$recipient) {
            return;
        }

        $recipient->notify(new NewMessageNotification($message));
    }
}
