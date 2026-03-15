<?php

declare(strict_types=1);

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $studentId = \Illuminate\Support\Facades\Auth::guard('recruting_student')->id();
        $documents = collect();

        if ($studentId > 0) {
            $documents = GlsDocument::query()
                ->where('student_id', '=', $studentId)
                ->orderByDesc('created_at')
                ->get();
        }


        return view('father.document', ['documents' => $documents,]);
    }

    public function sign(Request $request): View
    {
        return view('father.document_sign');
    }
}
