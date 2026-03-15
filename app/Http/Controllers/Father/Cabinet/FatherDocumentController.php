<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsDocument;
use App\Models\RecrutingStudent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FatherDocumentController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->guard('recruting_student')->user();
        $documents = [];

        if ($student instanceof RecrutingStudent && $student->id > 0) {
            // Автоматически создать договор, если у студента ещё нет ни одного contract-документа
            $hasContract = GlsDocument::where('student_id', $student->id)
                ->where('doc_type', 'contract')
                ->exists();

            if (!$hasContract) {
                $year   = now()->format('Y');
                $padded = str_pad((string) $student->id, 5, '0', STR_PAD_LEFT);
                GlsDocument::create([
                    'student_id' => $student->id,
                    'project_id' => $student->project_id ?? null,
                    'doc_no'     => "CON-{$year}-{$padded}",
                    'title'      => 'Umowa o świadczenie usług edukacyjnych',
                    'doc_status' => 'new',
                    'doc_type'   => 'contract',
                ]);
            }

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
            'full_name' => $student->parent_full_name ?: $student->full_name,
            'email' => $student->email,
        ];

        $contract = (object)[
            'signed' => in_array(strtolower(trim((string) $document->doc_status)), ['sign', 'signed'], true)
                || !is_null($document->sign_date),
            'subscription_amount' => (float) ($student->sum_aboniment ?? 0),
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

    public function download(Request $request, int $document)
    {
        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent || empty($student->id)) {
            abort(403, 'Unauthorized');
        }

        /** @var GlsDocument $document */
        $document = GlsDocument::query()
            ->where('student_id', $student->id)
            ->findOrFail($document);

        $isSigned = in_array(strtolower(trim((string) $document->doc_status)), ['sign', 'signed'], true)
            || !is_null($document->sign_date);

        $parentName = $student->parent_full_name ?: $student->full_name ?: ($student->email ?? '—');

        // Если PDF уже сохранён — отдаём из storage
        if ($document->pdf_path && Storage::disk('private')->exists($document->pdf_path)) {
            $filename = 'Document-' . ($document->doc_no ?? $document->id) . '.pdf';
            return Storage::disk('private')->download($document->pdf_path, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Генерируем PDF на лету из Blade-шаблона
        $pdf = Pdf::loadView('father.document.pdf.regulamin', [
            'document'   => $document,
            'student'    => $student,
            'parentName' => $parentName,
            'isSigned'   => $isSigned,
        ])
            ->setPaper('a4')
            ->setOption('defaultFont', 'DejaVu Sans');

        $pdfContent = $pdf->output();

        // Сохраняем в storage и обновляем pdf_path
        $filename = 'Document-' . ($document->doc_no ?? $document->id) . '.pdf';
        $path = 'documents/' . now()->format('Y/m') . '/' . $filename;
        Storage::disk('private')->put($path, $pdfContent);
        $document->update(['pdf_path' => $path]);

        return new Response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
