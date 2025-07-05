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
use Carbon\Carbon;

class PrivateStreamCountdownUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $streamRequest;
    public $timeRemaining;

    /**
     * Create a new event instance.
     */
    public function __construct(PrivateStreamRequest $streamRequest, int $timeRemaining)
    {
        $this->streamRequest = $streamRequest;
        $this->timeRemaining = $timeRemaining;
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
        return 'stream.countdown.update';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
            $this->streamRequest->requested_date->format('Y-m-d') . ' ' . $this->streamRequest->requested_time);
        $now = Carbon::now();
        $timeUntilStartSeconds = max(0, $scheduledTime->diffInSeconds($now, false));
        
        return [
            'time_remaining' => $this->timeRemaining,
            'time_until_user_can_join' => $this->streamRequest->getTimeUntilUserCanJoin(),
            'can_user_join' => $this->streamRequest->canUserJoin(),
            'can_streamer_start' => $this->streamRequest->canStreamerStart(),
            'can_start_actual_stream' => $this->streamRequest->canStartActualStream(),
            'is_in_preparation_period' => $this->streamRequest->isInPreparationPeriod(),
            'time_until_start_seconds' => $timeUntilStartSeconds,
            'time_until_start' => max(0, ceil($timeUntilStartSeconds / 60)),
            'timestamp' => now()->toISOString(),
        ];
    }
} 