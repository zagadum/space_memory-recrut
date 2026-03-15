<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FatherPortalController extends Controller
{
    public function index(Request $request)
    {
        // Получить студента из сессии (is_student middleware)
        $student = auth()->guard('student')->user();

        return view('father.parent_portal', compact('student'));
    }
}
