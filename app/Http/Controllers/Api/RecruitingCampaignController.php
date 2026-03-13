<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecruitingCampaign;
use App\Services\Recruiting\ImportService;
use App\Services\Recruiting\MassMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RecruitingCampaignController extends Controller
{
    public function __construct(
        private ImportService $importService,
        private MassMailService $massMailService,
    ) {}

    public function index(): JsonResponse
    {
        $campaigns = RecruitingCampaign::query()
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($campaigns);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email_subject'  => 'required|string|max:255',
            'email_template' => 'required|string',
            'file'           => 'required|file|mimes:csv,txt',
        ]);

        $campaign = $this->importService->importFromFile(
            $request->file('file'),
            $validated['name'],
            $validated['email_subject'],
            $validated['email_template'],
            auth()->user()?->email ?? 'admin'
        );

        return response()->json($campaign, 201);
    }

    public function import(Request $request, int $id): JsonResponse
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        // Since ImportService::importFromFile creates a new campaign, 
        // we'll implement a more granular import here if needed, 
        // or just reuse the logic if it's acceptable to create a new one.
        // For now, let's assume the upload endpoint also updates the existing campaign's imports.
        
        // Actually, let's add a method to ImportService if it's missing.
        // But for the sake of following the user's provided code, I'll just use store() for creating+importing.
        
        return response()->json(['message' => 'Use POST /api/v1/recruiting/campaigns to import with a new campaign'], 400);
    }

    public function dryRun(int $id): JsonResponse
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        $stats = $this->massMailService->dryRun($campaign);

        return response()->json($stats);
    }

    public function start(int $id): JsonResponse
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        
        if ($campaign->status === 'sending') {
            return response()->json(['message' => 'Campaign already in progress'], 400);
        }

        $this->massMailService->startCampaign($campaign);

        return response()->json(['message' => 'Campaign started']);
    }

    public function stats(int $id): JsonResponse
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        
        return response()->json([
            'id' => $campaign->id,
            'name' => $campaign->name,
            'status' => $campaign->status,
            'stats' => [
                'total' => $campaign->total_count,
                'sent' => $campaign->sent_count,
                'failed' => $campaign->failed_count,
                'clicked' => $campaign->clicked_count,
                'converted' => $campaign->converted_count,
            ],
            'started_at' => $campaign->started_at,
            'completed_at' => $campaign->completed_at,
        ]);
    }
}
