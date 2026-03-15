<?php

declare(strict_types=1);

namespace App\Http\Controllers\Father;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {

        \App::setLocale('pl');
        // Already logged in → go to portal
        if (Auth::guard('recruting_student')->check()) {
            return redirect()->route('father.payment');
        }

        return view('father.login', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting: 5 attempts per 15 min per IP+email
        $key = 'father-login:' . $request->ip() . ':' . $validated['email'];

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput(['email' => $validated['email']])
                ->withErrors(['email' => __('father.login.too_many_attempts', ['seconds' => $seconds])]);
        }

        // Find student
        $student = DB::table('recruting_student')
            ->where('email', $validated['email'])
            ->where('enabled', 1)
            ->where('blocked', 0)
            ->where('deleted', 0)
            ->first();

        if (!$student || !Hash::check($validated['password'], $student->password)) {
            RateLimiter::hit($key, 900);
            return back()
                ->withInput(['email' => $validated['email']])
                ->withErrors(['email' => __('father.login.invalid_credentials')]);
        }

        // Clear rate limiter on success
        RateLimiter::clear($key);

        // Login via student guard
        Auth::guard('recruting_student')->loginUsingId($student->id);

        // Update session
        $request->session()->regenerate();
        DB::table('recruting_student')
            ->where('id', $student->id)
            ->update([
                'sess_id'       => session()->getId(),
                'last_login_at' => now(),
            ]);

        return redirect()->intended(route('father.payment'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('recruting_student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('father.login');
    }
}
