<?php

namespace App\Console\Commands;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CronBlockStudent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all blocked records older the 2 month';

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
        $students = Student::where('blocking_date', '<=', Carbon::now()->subMonth(2)->toDateTimeString())
            ->where('deleted', '=', 0)
            ->where('blocked', '=', 1)
            ->get();
        foreach ($students as $student) {
            $student->deleted = 1;
            $student->blocking_reason = "Aвтоудаление(2мес блокировки)";
            $student->save();
        }
    }
}
