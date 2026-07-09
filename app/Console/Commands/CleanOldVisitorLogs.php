<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitorLog;
use Carbon\Carbon;

class CleanOldVisitorLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:clean-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up visitor logs that are older than 30 days to free up database storage.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting old logs cleanup...');
        
        $cutoffDate = Carbon::now()->subDays(30);
        
        $deletedCount = VisitorLog::where('created_at', '<', $cutoffDate)->delete();
        
        $this->info("Successfully deleted {$deletedCount} old visitor logs.");
    }
}
