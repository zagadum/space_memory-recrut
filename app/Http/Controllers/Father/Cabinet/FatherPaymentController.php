<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsDocument;
use App\Models\GlsInvoiceDocument;
use App\Models\GlsPaymentPlan;
use App\Models\GlsPaymentTransaction;
use App\Models\GlsProject;
use App\Models\RecrutingStudent;
use App\Services\ImojePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FatherPaymentController extends Controller
{
    public function index(Request $request)
    {
        /** @var RecrutingStudent $student */
        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent) {
            abort(403);
        }

        $contractDoc = GlsDocument::where('student_id', $student->id)
            ->where('doc_type', 'contract')
            ->orderByDesc('id')
            ->first();

        $contractSigned = $contractDoc
            && in_array(strtolower(trim((string) $contractDoc->doc_status)), ['sign', 'signed'], true);

        $contract = (object)[
            'signed'   => $contractSigned,
            'document' => $contractDoc,
        ];

        $project = GlsProject::query()
            ->when($student->project_id, fn ($query) => $query->where('id', $student->project_id))
            ->first()
            ?: GlsProject::query()->where('code', 'space_memory')->firstOrFail();

        $plans = GlsPaymentPlan::query()
            ->where('project_id', $project->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('months')
            ->get();

        $basePlan = $plans->firstWhere('months', 1) ?: $plans->sortBy('months')->first();

        $paymentPlans = $plans->map(function (GlsPaymentPlan $plan) use ($basePlan) {
            $basePrice = (float) ($basePlan?->price ?? $plan->price);
            $oldPrice = round($basePrice * max((int) $plan->months, 1), 2);
            $save = max($oldPrice - (float) $plan->price, 0);

            $plan->setAttribute('old_price', $oldPrice);
            $plan->setAttribute('save_amount', $save);

            return $plan;
        });

        $payments = GlsPaymentTransaction::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate 2-year payment schedule
        $firstSuccessfulPayment = $payments->where('status', 'completed')
            ->sortBy(fn($p) => $p->paid_at ?? $p->created_at)
            ->first();

        $totalMonthsPaid = (int)$payments->where('status', 'completed')->sum('months');
        $paymentSchedule = [];

        if ($firstSuccessfulPayment) {
            $startDate = ($firstSuccessfulPayment->paid_at ?? $firstSuccessfulPayment->created_at)->copy()->startOfMonth();

            for ($i = 0; $i < 24; $i++) {
                $monthDate = $startDate->copy()->addMonths($i);
                $paymentSchedule[] = [
                    'date'    => $monthDate,
                    'is_paid' => ($i < $totalMonthsPaid),
                    'label'   => $monthDate->translatedFormat('F Y'),
                ];
            }
        }

        return view('father.payment_process', compact(
            'student', 'contract', 'payments', 'project', 'paymentPlans', 'basePlan',
            'paymentSchedule', 'totalMonthsPaid'
        ));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer',
            'payment_plan_id' => 'required|integer|exists:gls_payment_plans,id',
        ]);

        /** @var RecrutingStudent $student */
        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent) {
            abort(403);
        }

        if ($student->id != $validated['student_id']) {
            abort(403);
        }

        $contractDoc = GlsDocument::query()
            ->where('student_id', $student->id)
            ->where('doc_type', 'contract')
            ->orderByDesc('id')
            ->first();

        $contractSigned = $contractDoc
            && in_array(strtolower(trim((string) $contractDoc->doc_status)), ['sign', 'signed'], true);

        if (!$contractSigned) {
            return back()->withErrors([
                'payment_plan_id' => 'Сначала необходимо подписать договор.',
            ]);
        }

        $project = GlsProject::query()
            ->when($student->project_id, fn ($query) => $query->where('id', $student->project_id))
            ->first()
            ?: GlsProject::query()->where('code', 'space_memory')->firstOrFail();

        $plan = GlsPaymentPlan::query()
            ->where('id', $validated['payment_plan_id'])
            ->where('project_id', $project->id)
            ->where('is_active', true)
            ->firstOrFail();

        $transactionTitle = sprintf(
            '%s — %s (%d занятий)',
            $project->name,
            $plan->period_label,
            $plan->lessons
        );

        [$transaction, $proforma] = DB::transaction(function () use ($student, $project, $plan, $transactionTitle) {
            $transaction = GlsPaymentTransaction::query()->create([
                'student_id'  => $student->id,
                'project_id'  => $project->id,
                'payment_plan_id' => $plan->id,
                'provider'    => 'imoje',
                'direction'   => 'in',
                'amount'      => (float) $plan->price,
                'currency'    => $plan->currency,
                'months'      => $plan->months,
                'lessons'     => $plan->lessons,
                'title'       => $transactionTitle,
                'status'      => 'pending',
            ]);

            $issueDate = now();

            $proforma = GlsInvoiceDocument::query()->create([
                'student_id' => $student->id,
                'project_id' => $project->id,
                'transaction_id' => $transaction->id,
                'document_type' => 'proforma',
                'number' => sprintf('PRO/%s/%s/%04d', strtoupper($project->code), $issueDate->format('Ym'), $transaction->id),
                'issue_date' => $issueDate->format('Y-m-d'),
                'sale_date' => $issueDate->format('Y-m-d'),
                'service_date_from' => $issueDate->copy()->startOfDay()->format('Y-m-d'),
                'service_date_to' => $issueDate->copy()->addMonths(max((int) $plan->months, 1))->subDay()->format('Y-m-d'),
                'title' => $transactionTitle,
                'amount_net' => round((float) $plan->price / 1.23, 2),
                'amount_gross' => (float) $plan->price,
                'currency' => $plan->currency,
                'meta' => [
                    'payment_plan_id' => $plan->id,
                    'months' => $plan->months,
                    'lessons' => $plan->lessons,
                ],
            ]);

            return [$transaction, $proforma];
        });

        $hashMethod = 'sha256';
        $serviceKey = (string) env('IMOJE_SERVICE_KEY');

        $fields = [
            'merchantId'          => (string) env('IMOJE_MERCHANT_ID'),
            'serviceId'           => (string) env('IMOJE_SERVICE_ID'),
            'amount'              => (int) round(((float) $plan->price) * 100),
            'currency'            => $plan->currency,
            'customerFirstName'   => $student->name ?? '',
            'customerLastName'    => $student->surname ?? '',
            'customerEmail'       => $student->email ?? '',
            'customerPhone'       => $student->parent_phone ?? '',
            'orderId'             => (string) $transaction->id,
            'customerId'          => (string) $student->id,
            'orderDescription'    => $this->buildImojeOrderDescription($project, $plan),
            'locale'              => 'pl',
            'urlSuccess'          => route('father.payment.success').'?orderId='.  $transaction->id,
            'urlFailure'          => route('father.payment.fail'),
            'urlNotification'     => route('imoje.webhook'),
        ];

        $imojeService = app(ImojePaymentService::class);
        $signature    = $imojeService->createSignature($fields, $serviceKey, $hashMethod) . ';' . $hashMethod;
        $fields['signature'] = $signature;

        $transaction->update([
            'provider_payload' => [
                'plan' => [
                    'id' => $plan->id,
                    'months' => $plan->months,
                    'lessons' => $plan->lessons,
                    'price' => (float) $plan->price,
                    'currency' => $plan->currency,
                ],
                'proforma_invoice_id' => $proforma->id,
                'imoje_fields' => $fields,
            ],
        ]);

        $payUrl = env('IMOJE_PAY_URL', 'https://sandbox.paywall.imoje.pl/payment');

        return view('father.payment_redirect', [
            'fields' => $fields,
            'payUrl' => $payUrl,
        ]);
    }

    public function success(Request $request)
    {
        /** @var RecrutingStudent $student */
        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent) {
            abort(403);
        }

        $orderId = $request->integer('orderId')
            ?: $request->integer('order_id')
            ?: $request->integer('id');

        $providerTransactionId = (string) ($request->input('transactionId')
            ?: $request->input('transaction_id')
            ?: '');

        $paymentsQuery = GlsPaymentTransaction::query()
            ->where('student_id', $student->id);

        $payment = null;

        if ($orderId > 0) {
            $payment = (clone $paymentsQuery)
                ->where('id', $orderId)
                ->first();
        }

        if (!$payment && $providerTransactionId !== '') {
            $payment = (clone $paymentsQuery)
                ->where('provider_transaction_id', $providerTransactionId)
                ->first();
        }

        if (!$payment) {
            $payment = (clone $paymentsQuery)
                ->where('status', 'completed')
                ->orderByDesc('paid_at')
                ->orderByDesc('created_at')
                ->first();
        }

        if (!$payment) {
            $payment = (clone $paymentsQuery)
                ->orderByDesc('created_at')
                ->first();
        }


        return view('father.payment_success', compact('student', 'payment'));
    }

    public function fail(Request $request)
    {
        /** @var RecrutingStudent $student */
        $student = auth()->guard('recruting_student')->user();

        if (!$student instanceof RecrutingStudent) {
            abort(403);
        }

        return view('father.payment_fail', compact('student'));
    }

    private function buildImojeOrderDescription(GlsProject $project, GlsPaymentPlan $plan): string
    {
        $raw = sprintf(
            '%s package %d months %d lessons',
            $project->name ?: ($project->code ?: 'Payment'),
            (int) $plan->months,
            (int) $plan->lessons
        );

        $ascii = Str::ascii($raw);
        $normalized = preg_replace('/\s+/', ' ', trim($ascii)) ?? '';
        $safe = preg_replace('/[^A-Za-z0-9\x{00C0}-\x{02C0}\s#&_\-"\',\.\/]/u', '', $normalized) ?? '';
        $safe = trim($safe);

        if ($safe === '') {
            $safe = sprintf('Order #%d %dM %dL', (int) $plan->id, (int) $plan->months, (int) $plan->lessons);
        }

        return mb_substr($safe, 0, 120);
    }
}
