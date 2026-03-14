<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Jobs\SendVerificationCodeJob;
use Illuminate\Support\Facades\Auth;

class StudentCabinetController extends Controller
{
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:6',
        ]);

        $student = DB::table('recruting_student')
            ->where('email', $request->email)
            ->where('verification_code', $request->code)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => __('api.invalid_code_or_email')
            ], 422);
        }

        $apiToken = Str::random(60);

        DB::table('recruting_student')
            ->where('id', $student->id)
            ->update([
                'verification_code' => null,
                'email_verified_at' => now(),
                'api_token'         => $apiToken,
                'enabled'           => 1,
            ]);

        // Standardize login for SPA/Portal
        $studentModel = Student::find($student->id);
        if ($studentModel) {
            Auth::guard('student')->login($studentModel);
        }

        return response()->json([
            'success'   => true,
            'api_token' => $apiToken,
            'message'   => __('api.email_successfully_verified')
        ]);
    }

    public function resendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $student = DB::table('recruting_student')
            ->where('email', $request->email)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => __('api.student_not_found')
            ], 404);
        }

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('recruting_student')
            ->where('id', $student->id)
            ->update([
                'verification_code' => $code,
                'updated_at'        => now(),
            ]);

        SendVerificationCodeJob::dispatch($student->email, $code);

        return response()->json([
            'success' => true,
            'message' => __('api.new_code_sent')
        ]);
    }

    public function showVerifyPage()
    {
        return view('registration.verify');
    }

    public function showCabinetPage()
    {
        return view('student.cabinet');
    }
}
