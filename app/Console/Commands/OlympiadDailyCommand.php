<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
class OlympiadDailyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'olympiad:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправлять нотификацию учителю, франчайзи, администратору и участнику на емейл за день до окончания олимпиады, то он оплатил и еще не прошел иона скоро закончиться';

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

        print 'Olympiad:dayly - start on date:'.$datePrevDay . PHP_EOL;
        $tomorrow = Carbon::now()->addDay()->toDateString(); // дата завтрашнего дня
        $olympiads = Olympiad::whereDate('end_date', $tomorrow)->get();
        $this->info('Found ' . $olympiads->count() . ' olympiad(s) ending on ' . $tomorrow);
        foreach ($olympiads as $olympiad) {
            $this->line("id: {$olympiad->id}, title: {$olympiad->title}, end_date: {$olympiad->end_date}");
            // далее — логика уведомлений (проверка оплаты, статуса участника и т.д.)
        }

        //        $studentList=Student::where('deleted', '=', 0)->where('blocked', '=', 0)->get();
//        foreach ($studentList as $student) {
//            print 'id:'.$student->id . PHP_EOL;
//            //HomeWorkService::SetBonusDoneDays($student->id,$datePrevDay);
//        }

        return 0;
    }
}
