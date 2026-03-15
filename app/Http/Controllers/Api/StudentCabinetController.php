<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\RecrutingStudent;
use App\Jobs\SendVerificationCodeJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class StudentCabinetController extends Controller
{

    //POST проверка кода
    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
        ]);

        $email = trim($validated['email']);
        $throttleKey = 'student-verify-code:' . Str::lower($email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return response()->json([
                'success' => false,
                'message' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($throttleKey)]),
            ], 429);
        }

        RateLimiter::hit($throttleKey, 900);

        $student = DB::table('recruting_student')
            ->where('email', $email)
            ->where('verification_code', $validated['code'])
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
            ->where('verification_code', $validated['code'])
            ->update([
                'verification_code' => null,
                'email_verified_at' => now(),
                'api_token'         => $apiToken,
                'enabled'           => 1,
            ]);

        RateLimiter::clear($throttleKey);

        // Standardize login for SPA/Portal
        $studentModel = RecrutingStudent::find($student->id);
        if ($studentModel) {
            Auth::guard('recruting_student')->login($studentModel);
        }

        return response()->json([
            'success'   => true,
            'api_token' => $apiToken,
            'message'   => __('api.email_successfully_verified')
        ]);
    }

    // Страница   повторно выслать код для входа
    public function resendCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = trim($validated['email']);
        $throttleKey = 'student-resend-code:' . Str::lower($email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            return response()->json([
                'success' => false,
                'message' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($throttleKey)]),
            ], 429);
        }

        RateLimiter::hit($throttleKey, 600);

        $student = DB::table('recruting_student')
            ->where('email', $email)
            ->first();

        if ($student) {
            do {
                $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            } while ($code === (string) $student->verification_code);

            DB::table('recruting_student')
                ->where('id', $student->id)
                ->update([
                    'verification_code' => $code,
                    'updated_at'        => now(),
                ]);

            SendVerificationCodeJob::dispatch($student->email, $code);
        }

        return response()->json([
            'success' => true,
            'message' => __('api.new_code_sent')
        ]);
    }

    // Страница ввод кода
    public function showVerifyPage()
    {
        $verifyFormToken = \Illuminate\Support\Str::random(64);
        session(['verify_form_token' => $verifyFormToken]);

        return view('registration.verify', [
            'verifyFormToken' => $verifyFormToken,
        ]);
    }

    public function showCabinetPage()
    {
        return view('student.cabinet');
    }
}
