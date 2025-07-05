<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\PrivateStream; // Add the PrivateStream model
use App\Models\Chat;

class LivePrivateStreamStarted implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $user;
    public $privateStream; 
    public $chat;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public function __construct( User $user , PrivateStream $privateStream , Chat $chat)
    {
        $this->user = $user;
        $this->privateStream = $privateStream; 
        $this->chat = $chat; 

        Log::info('Channel room-' . $user->username . ' online');
        $user->live_status = 'online';
        $user->save();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [new Channel('room-' . $this->user->username)];
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user,
            'streamerData' => $this->privateStream ,
            'chat' => $this->chat ,
            'channel' => 'room-' . $this->user->username
        ];
    }

    public function broadcastAs()
    {
        return 'private.livestream.started';
    }
    
}
