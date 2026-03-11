<?php

namespace App\Repositories\Payment;

use App\Models\PaymentDocument;

class PaymentDocumentRepository
{
    public function findInvoiceByNumber(string $fvnum): ?PaymentDocument
    {
        return PaymentDocument::query()
            ->where('doc_type', 'invoice')
            ->where('fvnum', $fvnum)
            ->first();
    }

    public function createRefund(array $data): PaymentDocument
    {
        return PaymentDocument::create([
            'student_id' => $data['programId'],
            'doc_type' => 'refund',
            'fvnum' => $data['fvnum'],
            'amount' => $data['amount'],
            'issue_date' => now()->toDateString(),
            'reason' => $data['reason'],
            'method' => $data['method'],
            'iban' => $data['iban'] ?? null,
            'status' => 'queued',
            'payload' => json_encode($data),
        ]);
    }

    public function upsertInvoice(array $data): PaymentDocument
    {
        $invoice = PaymentDocument::query()
            ->where('student_id', $data['programId'])
            ->where('doc_type', 'invoice')
            ->where('fvnum', $data['fvnum'])
            ->first();

        if (!$invoice) {
            return PaymentDocument::create([
                'student_id' => $data['programId'],
                'doc_type' => 'invoice',
                'fvnum' => $data['fvnum'],
                'amount' => $data['amount'],
                'issue_date' => $data['issueDate'],
                'pay_date' => $data['payDate'],
                'status' => 'updated',
                'payload' => json_encode($data),
            ]);
        }

        $invoice->fill([
            'amount' => $data['amount'],
            'issue_date' => $data['issueDate'],
            'pay_date' => $data['payDate'],
            'status' => 'updated',
            'payload' => json_encode($data),
        ]);
        $invoice->save();

        return $invoice;
    }

    public function createCorrection(array $data): PaymentDocument
    {
        return PaymentDocument::create([
            'student_id' => $data['programId'],
            'doc_type' => 'correction',
            'amount' => $data['amount'],
            'issue_date' => $data['corrDate'],
            'note' => $data['note'],
            'status' => 'created',
            'payload' => json_encode($data),
        ]);
    }

    public function createExtra(array $data): PaymentDocument
    {
        return PaymentDocument::create([
            'student_id' => $data['programId'],
            'doc_type' => 'extra',
            'amount' => $data['amount'],
            'issue_date' => $data['date'],
            'note' => $data['title'],
            'status' => 'created',
            'payload' => json_encode($data),
        ]);
    }
}

