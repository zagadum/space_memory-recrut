<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsDocument;
use App\Models\RecrutingStudent;
use Illuminate\Http\Request;

class FatherPortalController extends Controller
{
    public function index(Request $request)
    {
        // Получить студента из сессии (is_student middleware)
        $student = auth()->guard('recruting_student')->user();

        $contractDoc        = null;
        $hasSignedContract  = false;

        if ($student instanceof RecrutingStudent && $student->id > 0) {
            $contractDoc = GlsDocument::where('student_id', $student->id)
                ->where('doc_type', 'contract')
                ->orderByDesc('id')
                ->first();

            $hasSignedContract = $contractDoc
                && in_array(strtolower(trim((string) $contractDoc->doc_status)), ['sign', 'signed'], true);
        }

        return view('father.parent_portal', compact('student', 'contractDoc', 'hasSignedContract'));
    }
}
