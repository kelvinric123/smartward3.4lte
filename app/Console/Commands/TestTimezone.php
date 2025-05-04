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
        
        $now = Carbon::now();
        $utc = Carbon::now('UTC');
        
        $this->info('Current time (app timezone): ' . $now->toDateTimeString() . ' (' . $now->tzName . ')');
        $this->info('Current time (UTC): ' . $utc->toDateTimeString() . ' (UTC)');
        
        // Calculate offset hours manually
        $offset = $now->getTimestamp() - $utc->getTimestamp();
        $offsetHours = $offset / 3600;
        
        $this->info('Offset from UTC: ' . $offsetHours . ' hours (calculated)');
        $this->info('Timezone offset: ' . $now->offsetHours . ' hours (Carbon property)');
        
        return Command::SUCCESS;
    }
} 