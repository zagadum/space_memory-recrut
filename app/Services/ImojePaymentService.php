<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
class ImojePaymentService
{

    /**
     * @param array  $orderData
     * @param string $serviceKey
     * @param string $hashMethod
     *
     * @return string
     */
    public  function createSignature($orderData, $serviceKey, $hashMethod)
    {
        $data = $this->prepareData($orderData);
        return hash($hashMethod, $data . $serviceKey);
    }
    public function __construct()
    {
//        $cfg = config('services.imoje');
//        $this->merchantId = $cfg['merchant_id'];
//        $this->serviceId  = $cfg['service_id'];
//        $this->serviceKey = $cfg['service_key'];
//        $this->apiKey     = $cfg['api_key'];
//
//        // выбор базового URI: продакшн или мок
//        $base = $cfg['env'] === 'sandbox' ? 'https://private-anon-...apiary-mock.com/v1/merchant/'
//            : 'https://api.imoje.pl/v1/merchant/';
//
//        $this->client = new Client([
//            'base_uri' => rtrim($base, '/').'/',
//            'headers' => [
//                'Accept'        => 'application/json',
//                // токен из API Keys в панели Imoje :contentReference[oaicite:0]{index=0}
//                'Authorization' => 'Bearer '.$this->apiKey,
//            ],
//        ]);
    }

    /**
     * @param array  $data
     * @param string $prefix
     *
     * @return string
     */
    private function prepareData($data, $prefix = '') {
        ksort($data);
        $hashData = [];
        foreach($data as $key => $value) {
            if($prefix) {
                $key = $prefix . '[' . $key . ']';
            }
            if(is_array($value)) {
                $hashData[] = prepareData($value, $key);
            } else {
                $hashData[] = $key . '=' . $value;
            }
        }

        return implode('&', $hashData);
    }

    /**
     * @param array  $order
     * @param string $url
     * @param string $method
     * @param string $submitValue
     * @param string $submitClass
     * @param string $submitStyle
     *
     * @return string
     */
    public static function createOrderForm($order, $url = '', $method = '', $submitValue = '', $submitClass = '', $submitStyle = '') {
        if(!$submitValue) {
            $submitValue = 'Kontynuuj';
        }

        if(!$url) {
            $url = '';
        }

        if(!$method) {
            $method = 'POST';
        }

        $form = '<form method="' . $method . '" action="' . $url . '">';

        if(is_array($order)) {
            foreach($order as $key => $value) {
                $form .= '<div><input type="text" value="' . htmlentities($value) . '" name="' . $key . '" id="imoje_' . $key . '"></div>';
            }
        }

        $form .= '<button' . ($submitClass ? ' class="' . $submitClass . '"' : '') . ($submitStyle ? ' style="' . $submitStyle . '"' : '') . ' type="submit" id="submit-payment-form">' . $submitValue . '</button>';
        $form .= '</form>';

        return $form;
    }




    /**
     * Проверить статус платежа (например, по callback или вручную).
     *
     * @param string $paymentId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaymentStatus(string $paymentId): array
    {
        $endpoint = "{$this->merchantId}/payment/{$paymentId}";
        $resp = $this->client->get($endpoint);
        return json_decode((string)$resp->getBody(), true);
    }
}
