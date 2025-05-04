<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class TestTimezone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:timezone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the timezone settings for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing timezone settings:');
        $this->info('Current PHP timezone: ' . date_default_timezone_get());
        $this->info('Current Laravel app timezone: ' . config('app.timezone'));
        
        $now = now();
        $utc = now()->setTimezone('UTC');
        $kl = now()->setTimezone('Asia/Kuala_Lumpur');
        
        $this->info('App time: ' . $now->format('Y-m-d h:i:s A') . ' (' . $now->tzName . ')');
        $this->info('UTC time: ' . $utc->format('Y-m-d h:i:s A') . ' (UTC)');
        $this->info('KL time:  ' . $kl->format('Y-m-d h:i:s A') . ' (Asia/Kuala_Lumpur)');
        
        // Compare timestamps
        $utcHour = (int)$utc->format('H');
        $klHour = (int)$kl->format('H');
        
        $hourDiff = ($klHour - $utcHour + 24) % 24; // Handle day boundary crossings
        
        $this->info('Hour difference: KL is UTC+' . $hourDiff . ' hours');
        
        // Test with admission record
        $this->info('When creating an admission record with the current time:');
        $this->info('KL Time: ' . Carbon::now()->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d h:i:s A'));
        
        return Command::SUCCESS;
    }
} 