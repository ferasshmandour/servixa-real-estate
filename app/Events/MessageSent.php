<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public Message $message) {}

    /**
     * Broadcast on the conversation's private channel. Authorization for this
     * channel lives in routes/channels.php (participants only).
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('conversations.' . $this->message->conversation_id)];
    }

    /**
     * Client listens via `.listen('.message.sent', …)` — the leading dot opts
     * out of Laravel's default "App\Events\" namespace prefix.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $sender = $this->message->sender;

        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'content'         => $this->message->content,
            'status'          => $this->message->status,
            'created_at'      => $this->message->created_at->toIso8601String(),
            'sender_name'     => $sender
                ? trim(($sender->first_name ?? '') . ' ' . ($sender->last_name ?? ''))
                : '',
        ];
    }
}
