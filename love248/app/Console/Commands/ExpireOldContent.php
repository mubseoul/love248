<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Gallery;
use App\Models\Video;
use Carbon\Carbon;

class ExpireOldContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark galleries and videos as inactive if they haven\'t been refreshed in 30 days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting content expiration process...');

        // Get all expired galleries
        $expiredGalleries = Gallery::expired()->where('status', 1)->get();
        $galleryCount = $expiredGalleries->count();

        // Get all expired videos
        $expiredVideos = Video::expired()->where('status', 1)->get();
        $videoCount = $expiredVideos->count();

        if ($galleryCount > 0 || $videoCount > 0) {
            // Mark expired galleries as inactive
            if ($galleryCount > 0) {
                Gallery::expired()->where('status', 1)->update(['status' => 0]);
                $this->info("Marked {$galleryCount} galleries as inactive");
            }

            // Mark expired videos as inactive
            if ($videoCount > 0) {
                Video::expired()->where('status', 1)->update(['status' => 0]);
                $this->info("Marked {$videoCount} videos as inactive");
            }

            $this->info('Content expiration process completed successfully.');
        } else {
            $this->info('No expired content found.');
        }

        return 0;
    }
} 