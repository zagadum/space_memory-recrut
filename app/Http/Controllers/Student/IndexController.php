<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

class IndexController extends Controller {
    function dashboard(){
        $student = Auth::guard('recruting_student')->user();

        // Safe null-safety for teacher and group
        $teacher = $student ? $student->teacher : null;
        $group = $student ? $student->group : null;

        // Safe ranking fetch with try-catch and method_exists
        try {
            if ($student && method_exists($student, 'getTopStudents')) {
                $ranking = $student::getTopStudents();
            } else {
                $ranking = [];
            }
        } catch (\Exception $e) {
            $ranking = [];
        }

        // Stats array formation
        $stats = [
            'coins' => $student->coins ?? 0,
            'diamonds' => $student->diams ?? 0,
            'level' => $student->rang_level ?? 1
        ];

        return view('student.home.dashboard_father', compact('student', 'teacher', 'group', 'ranking', 'stats'));
    }
}
