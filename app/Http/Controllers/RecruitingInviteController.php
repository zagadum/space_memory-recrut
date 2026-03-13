<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\RecruitingStudentImport;
use App\Models\RecruitingCampaign;
use Illuminate\Http\RedirectResponse;

class RecruitingInviteController extends Controller
{
    /**
     * Person clicked the invite link in email.
     * Track click and redirect to registration completion page.
     */
    public function accept(string $token): RedirectResponse
    {
        $import = RecruitingStudentImport::query()->where('token', '=', $token)->firstOrFail();

        // Track click
        if (!$import->link_clicked_at) {
            $import->update([
                'link_clicked_at' => now(),
                'status'          => 'clicked',
            ]);

            // Increment campaign counter
            if ($import->campaign_id) {
                RecruitingCampaign::query()
                    ->where('id', '=', $import->campaign_id)
                    ->increment('clicked_count');
            }
        }

        // If already converted, redirect to login or dashboard
        if ($import->isConverted()) {
            return redirect('/father/document');
        }

        // Redirect to public registration completion page
        return redirect()->route('registration.complete', ['token' => $token]);
    }
}
