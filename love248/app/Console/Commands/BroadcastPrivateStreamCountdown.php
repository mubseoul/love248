<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrivateStreamRequest;
use App\Events\PrivateStreamCountdownUpdate;
use Carbon\Carbon;

class BroadcastPrivateStreamCountdown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'private-stream:broadcast-countdown';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast countdown updates for ongoing private streams';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get streams that are in preparation period or about to start
        $streams = PrivateStreamRequest::whereIn('status', ['accepted', 'in_progress'])
            ->whereNull('stream_ended_at')
            ->get();

        $broadcastCount = 0;

        foreach ($streams as $stream) {
            // Only broadcast for streams that are in active countdown periods
            if ($stream->shouldStartCountdown() || $stream->status === 'in_progress') {
                $scheduledTime = Carbon::createFromFormat('Y-m-d H:i:s', 
                    $stream->requested_date->format('Y-m-d') . ' ' . $stream->requested_time);
                
                $now = Carbon::now();
                
                // Calculate time remaining based on scheduled time, not actual start time
                $scheduledEndTime = $scheduledTime->copy()->addMinutes($stream->duration_minutes);
                
                // Only calculate countdown if we're at or past the scheduled time
                if ($now->gte($scheduledTime)) {
                    $timeRemaining = max(0, $scheduledEndTime->diffInSeconds($now, false));
                    
                    // Only broadcast if there's time remaining or if we need to show zero
                    if ($timeRemaining >= 0) {
                        broadcast(new PrivateStreamCountdownUpdate($stream, $timeRemaining));
                        $broadcastCount++;
                    }
                }
            }
        }

        $this->info("Broadcast countdown updates for {$broadcastCount} streams.");
        
        return 0;
    }
} 