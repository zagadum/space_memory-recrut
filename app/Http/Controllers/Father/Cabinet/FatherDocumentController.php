<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsInvoiceDocument;
use Illuminate\Http\Request;

class FatherDocumentController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->guard('recruting_student')->user();
        
        $documents = GlsInvoiceDocument::where('student_id', $student->id)->get();
        
        return view('father.documents', compact('student', 'documents'));
    }

    public function show(Request $request, int $document)
    {
        $student = auth()->guard('recruting_student')->user();
        
        $document = GlsInvoiceDocument::where('student_id', $student->id)->findOrFail($document);
        
        $parent = (object)[
            'full_name' => ($student->parent_name ?? $student->name ?? '') . ' ' . ($student->parent_surname ?? $student->surname ?? ''),
            'email' => $student->email,
        ];
        
        $contract = (object)[
            'signed' => !is_null($document->paid_at), // Example logic
            'subscription_amount' => $document->amount_gross,
        ];
        
        return view('father.document_view', compact(
            'student', 'document', 'parent', 'contract'
        ));
    }

    public function sign(Request $request)
    {
        $request->validate([
            'document_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);

        $student = auth()->guard('recruting_student')->user();
        
        if ($student->id != $request->student_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // TODO: В реальном проекте здесь должна быть таблица подписей или поле в акте
        // Для примера обновим дату оплаты или создадим лог
        
        return response()->json([
            'success' => true,
            'signed_at' => now()->toDateTimeString(),
            'ip' => $request->ip()
        ]);
    }
}
