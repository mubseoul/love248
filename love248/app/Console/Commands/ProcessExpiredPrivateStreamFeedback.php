<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PrivateStreamRequestController;

class ProcessExpiredPrivateStreamFeedback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'private-stream:process-expired-feedback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process private streams with expired feedback periods';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Processing expired private stream feedback periods...');
        
        $controller = new PrivateStreamRequestController();
        $result = $controller->processExpiredFeedback();
        
        $this->info("Processed {$result['processed']} expired stream feedback periods.");
        
        return 0;
    }
} 