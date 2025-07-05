<?php

namespace App\Events;

use App\Models\PrivateStreamRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateStreamStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $streamRequest;
    public $eventType;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(PrivateStreamRequest $streamRequest, string $eventType, array $data = [])
    {
        $this->streamRequest = $streamRequest;
        $this->eventType = $eventType;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('private-stream.' . $this->streamRequest->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'stream.state.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_type' => $this->eventType,
            'stream_request' => [
                'id' => $this->streamRequest->id,
                'status' => $this->streamRequest->status,
                'countdown_started_at' => $this->streamRequest->countdown_started_at,
                'actual_start_time' => $this->streamRequest->actual_start_time,
                'user_joined_at' => $this->streamRequest->user_joined_at,
                'user_joined' => $this->streamRequest->user_joined,
                'stream_ended_at' => $this->streamRequest->stream_ended_at,
            ],
            'timing' => [
                'canStreamerStart' => $this->streamRequest->canStreamerStart(),
                'canStartActualStream' => $this->streamRequest->canStartActualStream(),
                'canUserJoin' => $this->streamRequest->canUserJoin(),
                'isInPreparationPeriod' => $this->streamRequest->isInPreparationPeriod(),
                'timeUntilUserCanJoin' => $this->streamRequest->getTimeUntilUserCanJoin(),
            ],
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ];
    }
} 