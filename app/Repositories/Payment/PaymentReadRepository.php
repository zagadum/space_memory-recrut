<?php

namespace App\Repositories\Payment;

use App\Models\PaymentDocument;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Schema;

class PaymentReadRepository
{
    public function getStudentPayments(int $studentId)
    {
        return StudentPayment::query()
            ->where('student_id', $studentId)
            ->orderBy('date_pay')
            ->get();
    }

    public function getTransactions(int $programId): array
    {
        $baseTransactions = StudentPayment::query()
            ->where('student_id', $programId)
            ->orderByDesc('date_pay')
            ->get()
            ->map(static function (StudentPayment $payment): array {
                return [
                    'transactionId' => 'PAY-' . $payment->id,
                    'programId' => (int) $payment->student_id,
                    'date' => optional($payment->date_pay)->format('Y-m-d'),
                    'amount' => (float) $payment->sum_aboniment,
                    'kind' => 'payment',
                    'method' => $payment->type_pay,
                    'status' => (int) $payment->enabled === 1 ? 'paid' : 'pending',
                    'comment' => $payment->comment,
                ];
            })
            ->values()
            ->all();

        $docTransactions = [];
        if ($this->hasPaymentDocumentsTable()) {
            $docTransactions = PaymentDocument::query()
                ->where('student_id', $programId)
                ->whereIn('doc_type', ['refund', 'correction', 'extra'])
                ->orderByDesc('issue_date')
                ->orderByDesc('id')
                ->get()
                ->map(static function (PaymentDocument $doc): array {
                    return [
                        'transactionId' => 'DOC-' . $doc->id,
                        'programId' => (int) $doc->student_id,
                        'date' => optional($doc->issue_date)->format('Y-m-d'),
                        'amount' => (float) $doc->amount,
                        'kind' => $doc->doc_type,
                        'method' => $doc->method,
                        'status' => $doc->status,
                        'comment' => $doc->note,
                    ];
                })
                ->values()
                ->all();
        }

        return array_values(array_merge($baseTransactions, $docTransactions));
    }

    public function getKsefInvoices(int $programId): array
    {
        if (!$this->hasPaymentDocumentsTable()) {
            return [];
        }

        return PaymentDocument::query()
            ->where('student_id', $programId)
            ->where('doc_type', 'invoice')
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->get()
            ->map(static function (PaymentDocument $doc): array {
                return [
                    'id' => (int) $doc->id,
                    'programId' => (int) $doc->student_id,
                    'fvnum' => $doc->fvnum,
                    'issueDate' => optional($doc->issue_date)->format('Y-m-d'),
                    'payDate' => optional($doc->pay_date)->format('Y-m-d'),
                    'amount' => (float) $doc->amount,
                    'status' => $doc->status,
                ];
            })
            ->values()
            ->all();
    }

    private function hasPaymentDocumentsTable(): bool
    {
        return Schema::hasTable((new PaymentDocument())->getTable());
    }
}

