<?php

namespace App\Notifications;

use App\Models\Message;

class NewMessageNotification extends BaseNotification
{
    public function __construct(public Message $message) {}

    protected function slug(): string
    {
        return 'new_message';
    }

    protected function vars(mixed $notifiable): array
    {
        $sender  = $this->message->sender ?? null;
        $content = (string) ($this->message->content ?? '');

        return [
            'sender'  => trim(($sender->first_name ?? '') . ' ' . ($sender->last_name ?? '')),
            'preview' => mb_strlen($content) > 80 ? mb_substr($content, 0, 80) . '…' : $content,
        ];
    }

    protected function payload(mixed $notifiable): array
    {
        return [
            'message_id'      => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'deeplink'        => '/conversations/' . $this->message->conversation_id,
        ];
    }
}
