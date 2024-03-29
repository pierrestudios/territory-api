<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;

class ScheduleList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List when scheduled commands are executed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
		parent::__construct();
		$this->schedule = $schedule;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $events = array_map(function ($event) {
            return [
                'cron'    => $event->expression,
                'command' => static::fixupCommand($event->command),
                'next' => $event->nextRunDate()->toDateTimeString(),
            ];
        }, $this->schedule->events());

        $this->table(
            ['Cron', 'Command', 'Next Run', 'Previous Run'],
            $events
        );
    }

    /**
     * If it's an artisan command, strip off the PHP
     *
     * @param  $command
     * @return string
     */
    protected static function fixupCommand($command)
    {
        $parts = explode(' ', $command);
        if (count($parts) > 2 && $parts[1] === "'artisan'") {
            array_shift($parts);
        }

        return implode(' ', $parts);
    }
}
