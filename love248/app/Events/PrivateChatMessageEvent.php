<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;
use App\Models\PrivateStream; // Add the PrivateStream model

class PrivateChatMessageEvent implements ShouldBroadcastNow
{
    use SerializesModels;

    public $chat;
    public $privateStream; // Add the streamerData variable

    /**
     * Create a new event instance.
     *
     * @param Chat $chat
     * @param PrivateStream $streamerData
     * @return void
     */
    public function __construct(Chat $chat, PrivateStream $privateStream)
    {
        $this->chat = $chat;
        $this->privateStream = $privateStream; // Assign the streamerData
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel($this->chat->roomName);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'chat' => $this->chat,
            'streamerData' => $this->privateStream // Include streamerData in the broadcast
        ];
    }

    /**
     * Broadcast event name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'private-chat-message';
    }
}
