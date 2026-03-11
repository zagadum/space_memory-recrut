<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BonusWeekCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:week';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Начисление бонусов за выполненные задания Недельный бонус';

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
        print 'bonus:week - start' . PHP_EOL;
        return 0;
    }
}
