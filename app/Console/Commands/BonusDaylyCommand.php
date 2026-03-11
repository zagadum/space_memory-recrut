<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Services\Game\HomeWorkService;
use Illuminate\Console\Command;

class BonusDaylyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Дневной бонус - начисление бонусов за выполненные задания';

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
        $datePrevDay=date('Y-m-d',strtotime("-1 day"));

        print 'bonus:dayly - start on date:'.$datePrevDay . PHP_EOL;
//        $studentList=Student::where('deleted', '=', 0)->where('blocked', '=', 0)->get();
//        foreach ($studentList as $student) {
//            print 'id:'.$student->id . PHP_EOL;
//            //HomeWorkService::SetBonusDoneDays($student->id,$datePrevDay);
//        }

        return 0;
    }
}
