<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GlsPaymentTransaction;
use App\Models\GlsProject;
use App\Services\ImojePaymentService;
use Illuminate\Http\Request;

class FatherPaymentController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->guard('student')->user();
        
        // TODO: проверить подписан ли договор
        $contract = (object)[
            'signed' => true, // Example
        ];

        $payments = GlsPaymentTransaction::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $periods = [
            ['months' => 1, 'lessons' => 4, 'price' => 440, 'old' => 490, 'popular' => false, 'save' => 0],
            ['months' => 3, 'lessons' => 12, 'price' => 1180, 'old' => 1470, 'popular' => true, 'save' => 290],
            ['months' => 9, 'lessons' => 36, 'price' => 3160, 'old' => 4410, 'popular' => false, 'save' => 1250],
        ];

        return view('father.payment_process', compact(
            'student', 'contract', 'payments', 'periods'
        ));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|integer',
            'months'     => 'required|in:1,3,9', // Adjusted to match periods
            'amount'     => 'required|numeric|min:1',
            'lessons'    => 'required|integer',
        ]);

        $student = auth()->guard('student')->user();

        if ($student->id != $validated['student_id']) {
            abort(403);
        }

        $project = GlsProject::where('code', 'space_memory')->firstOrFail();

        // 1. Создать запись платежа в БД (status = 'pending')
        $transaction = GlsPaymentTransaction::create([
            'student_id'  => $student->id,
            'project_id'  => $project->id,
            'provider'    => 'imoje',
            'direction'   => 'in',
            'amount'      => (float)$validated['amount'],
            'currency'    => 'PLN',
            'status'      => 'pending',
        ]);

        // 2. Сформировать Imoje payload
        $hashMethod = 'sha256';
        $serviceKey = (string)env('IMOJE_SERVICE_KEY');

        $fields = [
            'merchantId'          => (string)env('IMOJE_MERCHANT_ID'),
            'serviceId'           => (string)env('IMOJE_SERVICE_ID'),
            'amount'              => (int) ($validated['amount'] * 100), // grosze
            'currency'            => 'PLN',
            'customerFirstName'   => $student->name ?? '',
            'customerLastName'    => $student->surname ?? '',
            'customerEmail'       => $student->email ?? '',
            'customerPhone'       => $student->parent_phone ?? '',
            'orderId'             => (string) $transaction->id,
            'customerId'          => (string) $student->id,
            'orderDescription'    => $project->name . ' — ' . $validated['months'] . ' msc',
            'locale'              => 'pl',
            'urlSuccess'          => url('/father/payment-success'),
            'urlFailure'          => url('/father/payment-fail'),
            'urlNotification'     => url('/payments/imoje/webhook'),
        ];

        $imojeService = app(ImojePaymentService::class);
        $signature    = $imojeService->createSignature($fields, $serviceKey, $hashMethod) . ';' . $hashMethod;
        $fields['signature'] = $signature;

        $payUrl = env('IMOJE_PAY_URL', 'https://sandbox.paywall.imoje.pl/payment');

        // 3. Redirect на Imoje payment URL (via hidden form in payment_redirect pattern or direct away)
        // Here we can use a helper view that auto-submits like the previous pattern but as a production controller we can also redirect if it was a direct form post.
        
        return view('father.payment_redirect', [
            'fields' => $fields,
            'payUrl' => $payUrl,
        ]);
    }

    public function success(Request $request)
    {
        $student = auth()->guard('student')->user();
        
        $payment = GlsPaymentTransaction::where('student_id', $student->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return view('father.payment_success', compact('student', 'payment'));
    }
}
