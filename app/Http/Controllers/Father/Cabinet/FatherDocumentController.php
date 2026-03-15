<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsDocument;
use App\Models\RecrutingStudent;
use Illuminate\Http\Request;

class FatherDocumentController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->guard('recruting_student')->user();
        $documents = [];

        if ($student instanceof RecrutingStudent && $student->id > 0) {
            $documents = GlsDocument::query()
                ->where('student_id', $student->id)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('father.documents', compact('student', 'documents'));
    }

    public function show(Request $request, int $document)
    {
        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent || empty($student->id)) {
            abort(403, 'Unauthorized');
        }

        $document = GlsDocument::query()
            ->where('student_id', $student->id)
            ->findOrFail($document);

        $parent = (object)[
            'full_name' => ($student->parent_name ?? $student->name ?? '') . ' ' . ($student->parent_surname ?? $student->surname ?? ''),
            'email' => $student->email,
        ];

        $contract = (object)[
            'signed' => in_array(strtolower(trim((string) $document->doc_status)), ['sign', 'signed'], true)
                || !is_null($document->sign_date),
            'subscription_amount' => 0,
        ];

        return view('father.document_view', compact('student', 'document', 'parent', 'contract'));
    }

    public function sign(Request $request)
    {
        $request->validate([
            'document_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);

        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent || $student->id != $request->student_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $document = GlsDocument::query()
            ->where('id', $request->integer('document_id'))
            ->where('student_id', $student->id)
            ->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $normalizedStatus = strtolower(trim((string) $document->doc_status));
        if (!in_array($normalizedStatus, ['sign', 'signed'], true)) {
            $document->update([
                'doc_status' => 'sign',
                'sign_date' => now(),
            ]);
            $document->refresh();
        } elseif (is_null($document->sign_date)) {
            $document->update(['sign_date' => now()]);
            $document->refresh();
        }

        return response()->json([
            'success' => true,
            'signed_at' => optional($document->sign_date)->format('Y-m-d H:i:s'),
            'doc_status' => $document->doc_status,
        ]);
    }
}
