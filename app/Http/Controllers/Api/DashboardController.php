<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        // Temporary aggregate payload for frontend dashboard cards.
        return response()->json([
            'totalStudents' => 0,
            'activeGroups' => 0,
            'pendingInvoices' => 0,
            'newLeads' => 0,
        ]);
    }
}

