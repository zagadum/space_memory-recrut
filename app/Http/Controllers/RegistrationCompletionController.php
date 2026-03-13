<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RecruitingStudentImport;
use App\Models\RecruitingCampaign;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegistrationCompletionController extends Controller
{
    /**
     * Show the public registration completion form.
     */
    public function index(string $token)
    {
        $import = RecruitingStudentImport::query()
            ->where('token', '=', $token)
            ->where('status', '!=', 'converted')
            ->firstOrFail();

        return view('registration.complete', compact('import', 'token'));
    }

    /**
     * Process registration completion.
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $import = RecruitingStudentImport::query()
            ->where('token', '=', $token)
            ->where('status', '!=', 'converted')
            ->firstOrFail();

        $email = $import->email;
        $existing = DB::table('recruting_student')->where('email', $email)->first();

        if ($existing) {
            $status = $existing->status ?? 'unknown';
            
            return match ($status) {
                'registered', 'paid' => back()->withErrors([
                    'email' => __('recruiting.email_exists_registered'),
                ])->with('show_login_link', true),
                
                'new' => back()->withErrors([
                    'email' => __('recruiting.email_exists_incomplete'),
                ])->with('show_resend_link', true)->with('student_email', $email),
                
                'archived', 'expelled' => back()->withErrors([
                    'email' => __('recruiting.email_exists_archived'),
                ]),
                
                default => back()->withErrors([
                    'email' => __('recruiting.email_exists_generic'),
                ]),
            };
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'consent_data' => ['required', 'accepted'],
            'consent_policy' => ['required', 'accepted'],
            'consent_photo' => ['required', 'accepted'],
        ]);

        DB::transaction(function () use ($import, $request) {
            // Convert to student in the legacy/student table used by auth.
            $student = Student::create([
                'name'          => $import->name ?? '',
                'email'         => $import->email,
                'surname'       => $import->surname ?? '',
                'lastname'      => $import->name ?? '',
                'phone'         => $import->phone ?? '',
                'subject'       => $import->subject ?? '',
                'password'      => Hash::make($request->password),
                'country_id'    => $this->mapCountry($import->country),
                'locality'      => $import->city ?? '',
                'enabled'       => 1,
                'blocked'       => 0,
                'deleted'       => 0,
            ]);


            // Mark import as converted
            $import->update([
                'status'               => 'converted',
                'converted_at'         => now(),
                'converted_student_id' => $student->id,
            ]);

            // Increment campaign counter
            if ($import->campaign_id) {
                RecruitingCampaign::query()
                    ->where('id', '=', $import->campaign_id)
                    ->increment('converted_count');
            }
        });

        // Authenticate as student
        $student = Student::query()->where('email', '=', $import->email)->first();
        Auth::guard('student')->login($student);

        return redirect('/father/document')->with('success', __('recruiting.registration.success'));
    }

    private function mapCountry(?string $countryName): ?int
    {
        if (!$countryName) return null;
        return DB::table('country')->where('name', 'like', "%{$countryName}%")->value('id');
    }
}
