<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ImojePaymentService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use MemoryOlympiad\Models\Olympiad\MOlympiad;
use MemoryOlympiad\Models\Olympiad\MOlympiadSubscribe;
use MemoryOlympiad\Models\Olympiad\MPayment;
use MemoryOlympiad\Models\Olympiad\MParticipant;

/**
 * Контроллер для обработки платежей через iMoje
 * https://sandbox.imoje.ing.pl/
 *
 * Visa   4111111111111111   12   29   123   no   Positive authorization - profile transaction completed
 * Visa   4485201817664006   12   29   123   no   Positive authorization - profile transaction rejected, dcc rejected
 * Visa   4444333322221111   12   29   123   no   Negative authorisation
 * Visa   4012001037141112   12   29   123   yes   Positive authorization - profile transaction completed
 * Visa   4749601201390567   12   29   123   yes   Positive authorization - profile transaction rejected
 * Visa   4934403892699132   12   29   123   yes   Negative authorisation
 * Visa   4012001007002005   12   29   123   no   Provider error
 * Visa   4282513338596268   12   29   123   no   Positive authorization - profile provider transaction error
 *
 */
class ImojeController extends Controller
{

    protected $imojePaymentService;

    public function __construct(ImojePaymentService $imojePaymentService)
    {
        $this->imojePaymentService = $imojePaymentService;
    }

    //--- Успешная оплата
    public function paySuccess(){
        return view('payments.imoje.success');
    }

    //--- Не успешная оплата
    public function payFailure(){
        return view('payments.imoje.failure');
    }
    public function payTest(){

//https://bump.sh/pgw/doc/imoje-paywall-en/topic/topic-notifications
        $practicant_id=4;
        $olympiad_id =1;
        $Olympiad=MOlympiad::where('id',$olympiad_id)->first();
        if (empty($Olympiad['id'])){
            die('Олимпиада не найдена');
        }
       // dd($Olympiad['status']);
//        if ($Olympiad['status']=='draft' || $Olympiad['status']=='completed'){
//            die('Олимпиада закынта для регистрации');
//        }
        if (empty($Olympiad['international_currency'])){
            die('В олимпиаде не указана валюта международной оплаты');
        }
        if ($Olympiad['international_price']>0) {
            $amount = $Olympiad->international_price;
        }
        $Practicant=MParticipant::find($practicant_id);
        $payment = MPayment::where('practicant_id', $Practicant->id)
            ->where('olympiad_id', $olympiad_id)
            ->whereIn('status', ['new', 'ok'])
            ->first();
        $createNewOrder=false;
        if (empty($payment)){
            $createNewOrder=true;
        }


        if (isset($payment) && $payment['status']=='ok') {
            die('Вы уже оплатили участие в этой олимпиаде');
        }

        if ($createNewOrder) {
            $Payment = MPayment::create([
                'practicant_id' => $Practicant->id,
                'olympiad_id' => $olympiad_id,
                // 'payment_type' => 'imoje',
                'payment_date' => now(),
                'amount' => $amount ,
                'currency' => $Olympiad['international_currency'],
                'status' => 'new',
                'provider' => 'imoje',
            ]);
            $orderId = $Payment->id;
        }else{
            $orderId = $payment->id;
        }
        $hashMethod = 'sha256';

        $serviceKey =   env('IMOJE_SERVICE_KEY');

        Log::channel('imoje')->info('iMoje : create_order', [
            'orderId' => $orderId,
            'practicant_id' =>  $Practicant->id,
            'olympiad_id' => $olympiad_id,
        ]);


        $fields = [
            'merchantId' =>  env('IMOJE_MERCHANT_ID'),
            'serviceId' =>  env('IMOJE_SERVICE_ID'),
            'amount' => $amount * 100,//сумма *100
            'currency' => $Olympiad['international_currency'],
            'customerFirstName' => $Practicant->surname,
            'customerLastName' => $Practicant->lastname,
            'customerEmail' => $Practicant->email,
            'customerPhone' => $Practicant->phone,
            'orderId' => $orderId,
            'customerId' => $Practicant->id,
            //'olympiad_id' => $olympiad_id,
            //
            // 'additionalDescription' => 'Details of products or services ordered ',



            'orderDescription' => 'For participation in the Olympics #'.$olympiad_id ,
            'locale' => 'uk',
             'urlSuccess' => 'https://memory.firm.kiev.ua/payments/imoje/success', //URL the user will be redirected to after a successfully completed transaction.
             'urlFailure' => 'https://memory.firm.kiev.ua/payments/imoje/failure',
             'urlReturn' => 'https://memory.firm.kiev.ua/payments/imoje/test',
            //????? не срабатывает
             'urlNotification' => 'https://memory.firm.kiev.ua/api/payments/imoje/webhook', //Dynamic notification address, possibility to set a specific address for a single transaction.
            //visibleMethod  // {  card/wallet/pbl/blik/imoje_paylater/wt/lease
        ];
        $signature = $this->imojePaymentService->createSignature($fields, $serviceKey, $hashMethod) . ';' . $hashMethod;
        $fields['signature'] = $signature;
        print ' orderId #'.$fields['orderId'].'<br>';
        print ' '.$fields['amount']/100 .' '.$fields['currency'];
        print $this->imojePaymentService->createOrderForm(
            $fields,  env('IMOJE_PAY_URL'),
            'POST',
            'Сплатити зараз',
            'btn btn-primary',
            ''
        );
        die;
        return view('payments.imoje.test',['fields'=>$fields,'signature'=>$signature]);
    }



    // Вебхук от iMoje со статусом транзакции
    public function webhook(Request $request)
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('X-Imoje-Signature', '');

        Log::channel('imoje')->info('iMoje webhook: получено уведомление', [
            'signature_header' => $signature,
            'body_preview' => mb_substr($rawBody, 0, 500)
        ]);
        // Подпись вида: merchantid=...;serviceid=...;signature=...;alg=sha256
        $parts = collect(explode(';', (string)$signature))
            ->mapWithKeys(function ($part) {
                [$k, $v] = array_pad(explode('=', trim($part), 2), 2, null);
                return [strtolower($k) => $v];
            });

        $incoming = $parts->get('signature');
        $alg      = $parts->get('alg', 'sha256');

        // Хэш по формуле: hash(alg, body + service_key)
        $serviceKey = env('IMOJE_SERVICE_KEY');
        $own = hash($alg, $rawBody.$serviceKey);

        if (!hash_equals((string)$incoming, (string)$own)) {
            Log::channel('imoje')->warning('iMoje webhook: подпись НЕ сошлась');
            // Игнорируем неверные уведомления
            return response()->json(['status' => 'ignored'], 400);
        }

        $payload = $request->json()->all();

        // Примеры статусов: new|pending|settled|cancelled|rejected (transaction/payment)
        $txStatus = data_get($payload, 'transaction.status') ?? data_get($payload, 'payment.status');
        $orderId  = data_get($payload, 'transaction.orderId') ?? data_get($payload, 'payment.orderId');
        $amount  = data_get($payload, 'transaction.amount') ?? data_get($payload, 'payment.amount');
        $currency  = data_get($payload, 'transaction.currency') ?? data_get($payload, 'payment.currency');
        $paymentMethod  = data_get($payload, 'transaction.paymentMethod') ?? data_get($payload, 'payment.paymentMethod');
        $paymentMethodCode  = data_get($payload, 'transaction.paymentMethodCode') ?? data_get($payload, 'payment.paymentMethodCode');
        $transaction_id  = data_get($payload, 'transaction.id') ?? data_get($payload, 'payment.id');

        $statusCode  = data_get($payload, 'transaction.statusCode') ?? data_get($payload, 'payment.statusCode');
        $statusCodeDescription  = data_get($payload, 'transaction.statusCodeDescription') ?? data_get($payload, 'payment.statusCodeDescription');

        Log::channel('imoje')->info('iMoje webhook: проверено и приняты данные', [
            'orderId' => $orderId,
            'txStatus' => $txStatus,
            'payload' => $payload
        ]);
        if ($txStatus=='settled' || $txStatus=='ok') {
            $txStatus = 'ok';
            $Mpaymnet=MPayment::where('id',$orderId)->first();
            if ($amount<$Mpaymnet['amount']*100 || $currency!=$Mpaymnet['currency']) {
                Log::channel('imoje')->warning('iMoje webhook: сумма оплаты меньше ожидаемой', [
                    'orderId' => $orderId,
                    'received_amount' => $amount,
                    'received_currency' => $currency,
                    'expected_amount' => $Mpaymnet['amount']*100
                ]);
                return response()->json(['status' => 'ignored'], 400);
            }

            $updateStatus['is_pay']=1;
        }


        if (!empty($paymentMethod)){
            $updateStatus['paymentMethod']=$paymentMethod;
        }
        if (!empty($statusCodeDescription)){
            $updateStatus['statusCodeDescription']=$statusCodeDescription;
        }
        if (!empty($statusCode)){
            $updateStatus['statusCode']=$statusCode;
        }

        if (!empty($paymentMethodCode)){
            $updateStatus['paymentMethodCode']=$paymentMethodCode;
        }
        if (!empty($transaction_id)){
            $updateStatus['transaction_id']=$transaction_id;
        }

        $updateStatus['status']=$txStatus;
        MPayment::where('id',$orderId)->update($updateStatus);
        if ($txStatus=='ok' || $txStatus=='settled') {
            $Mpaymnet=MPayment::where('id',$orderId)->first();
            MOlympiadSubscribe::where('practicant_id',$Mpaymnet['practicant_id'])
                ->where('olympiad_id',$Mpaymnet['olympiad_id'])
                ->update(['is_pay'=>1]);

        }
        // ВАЖНО: вернуть 200 OK и {"status":"ok"}, чтобы iMoje прекратил ретраи
        return response()->json(['status' => 'ok']);
    }
}
