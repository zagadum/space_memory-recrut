<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecruitingCampaign;
use App\Services\Recruiting\ImportService;
use App\Services\Recruiting\MassMailService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Recruiting\StoreCampaignRequest;
use App\Http\Requests\Recruiting\ImportStudentsRequest;
use App\Http\Resources\Recruiting\RecruitingCampaignResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class RecruitingCampaignController extends Controller
{
    public function __construct(
        private ImportService $importService,
        private MassMailService $massMailService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $campaigns = RecruitingCampaign::query()
            ->orderByDesc('created_at')
            ->paginate(20);

        return RecruitingCampaignResource::collection($campaigns);
    }

    public function store(StoreCampaignRequest $request): RecruitingCampaignResource
    {
        $validated = $request->validated();

        $campaign = $this->importService->importFromFile(
            $request->file('file'),
            $validated['name'],
            $validated['email_subject'],
            $validated['email_template'],
            auth()->user()?->email ?? 'admin'
        );

        return new RecruitingCampaignResource($campaign);
    }

    public function import(ImportStudentsRequest $request, int $id): JsonResponse
    {
        // For now, let's keep the user's intended fallback message but localized
        return response()->json([
            'message' => __('recruiting.campaign.import_new_only')
        ], 400);
    }

    public function dryRun(int $id): JsonResponse
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        $stats = $this->massMailService->dryRun($campaign);

        // CamelCase the stats keys manually since it's a raw array from service
        return response()->json([
            'validEmails'         => $stats['valid_emails'],
            'invalidEmails'       => $stats['invalid_emails'],
            'duplicateInStudents' => $stats['duplicate_in_students'],
            'readyToSend'         => $stats['ready_to_send'],
        ]);
    }

    public function start(int $id): JsonResponse
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        
        if ($campaign->status === 'sending') {
            return response()->json([
                'message' => __('recruiting.campaign.already_in_progress')
            ], 400);
        }

        $this->massMailService->startCampaign($campaign);

        return response()->json([
            'message' => __('recruiting.campaign.started')
        ]);
    }

    public function stats(int $id): RecruitingCampaignResource
    {
        $campaign = RecruitingCampaign::findOrFail($id);
        
        return new RecruitingCampaignResource($campaign);
    }
}
