<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RecruitingStudentImport;
use App\Models\RecruitingCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecruitingInviteController extends Controller
{
    /**
     * Person clicked the invite link in email.
     * Move record from import → recruting_student, redirect to registration.
     */
    public function accept(string $token): RedirectResponse
    {
        $import = RecruitingStudentImport::query()
            ->where('token', '=', $token)
            ->firstOrFail();

        // Track click
        if (!$import->link_clicked_at) {
            $import->update([
                'link_clicked_at' => now(),
                'status'          => 'clicked',
            ]);

            // Increment campaign counter
            if ($import->campaign_id) {
                RecruitingCampaign::query()
                    ->where('id', $import->campaign_id)
                    ->increment('clicked_count');
            }
        }

        // If already converted, just redirect
        if ($import->isConverted()) {
            return redirect('/register?email=' . urlencode($import->email));
        }

        // Convert: create record in recruting_student
        $studentId = DB::table('recruting_student')->insertGetId([
            'email'      => $import->email,
            'name'       => $import->name ?? '',
            'surname'    => $import->surname ?? '',
            'phone'      => $import->phone ?? '',
            'subject'    => $import->subject ?? '',
            'status'     => 'new',
            'password'   => Hash::make(Str::random(12)),
            'enabled'    => 0,
            'blocked'    => 0,
            'deleted'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mark as converted
        $import->update([
            'status'               => 'converted',
            'converted_at'         => now(),
            'converted_student_id' => $studentId,
        ]);

        // Increment campaign counter
        if ($import->campaign_id) {
            RecruitingCampaign::query()
                ->where('id', $import->campaign_id)
                ->increment('converted_count');
        }

        // Redirect to registration page with pre-filled email
        return redirect('/register?email=' . urlencode($import->email) . '&token=' . $token);
    }
}
