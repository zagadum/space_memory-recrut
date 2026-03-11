<?php

namespace App\Helpers;



use App\Models\StudentTrainingTask;
use App\Models\TraningOnline;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\App;
class SiteHelper {
    var $lang;


    /**
     * Get supported locales
     *
     * @param bool $forDisplay Whether to return uppercase display format (UA, PL, EN) or database format (uk, pl, en)
     * @param bool $includeEnglish Whether to include English locale
     * @return \Illuminate\Support\Collection
     */
    public static function Locales(bool $forDisplay = false, bool $includeEnglish = false)
    {
        $locales = $includeEnglish
            ? \App\Services\LocaleService::getSupportedLocales()
            : ['uk', 'pl','en'];

        if ($forDisplay) {
            return collect($locales)->map(function ($locale) {
                return \App\Services\LocaleService::toDisplay($locale);
            });
        }

        return collect($locales);
    }

    public static function GetLang(){
        if (isset($_COOKIE['lang'])){
            return $_COOKIE['lang'];
        }else{
            return  App::currentLocale();
        }
    }
    public static function IsAdmin() {
        return self::is_admin();
    }



    public static function is_admin() {
        //$user = Auth::user();
        $user = Auth::guard('admin')->user();
        return (!empty($user->is_admin) && $user->is_admin === 1);
    }








    public static function get_geolocation($apiKey, $ip, $lang = "en", $fields = "*", $excludes = "") {
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;
        $cURL = curl_init();

        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: '.$_SERVER['HTTP_USER_AGENT']
        ));

        return curl_exec($cURL);
    }
    public static function converTime($restInSec){
        $restInSec=round($restInSec);
        $minuteSeconds = $restInSec % 60;

        $minutes = (int)floor($restInSec / 60);
        $remainingSeconds = $minuteSeconds % 60;
        $seconds =(int) ceil($remainingSeconds);


        $obj = array('m' => str_pad(  $minutes,2,'0',STR_PAD_LEFT), 's' => str_pad($seconds,2,'0',STR_PAD_LEFT));
        return $obj['m'].':'. $obj['s'];
    }


    public static function GetIPInfo(){
        $apiKey = "0647763beb944bc8ac9abccc7c588224";
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($ip=='127.0.0.1'){
            return 'UA';
        }
        $location = self::get_geolocation($apiKey, $ip);
        $decodedLocation = json_decode($location, true);

        // Check if API returned valid response with country_code2
        if (isset($decodedLocation['country_code2'])) {
            return $decodedLocation['country_code2'];
        }

        // Log API failure for debugging
        \Log::warning('Geolocation API failed or returned invalid response', [
            'ip' => $ip,
            'response' => $decodedLocation
        ]);

        // Default to UA if API fails or returns error
        return 'UA';
    }
    public static function mpdfParams()
    {
        $params_mpdf=[
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => '3',
            'margin_top' => '30',
            'margin_bottom' => '20',
            'margin_footer' => '2',
            'margin_right'=>5,
            'margin_left'=>5,
            'tempDir' => storage_path('framework/cache'),
            'fontDir' => [public_path('fonts')],
            'default_font' => 'days',
            'fontdata' => [
                'roboto' => [
                    'R' => 'Roboto-Medium.ttf', // Обычный
                    'B' => 'Roboto-Bold.ttf',    // Жирный (если есть)
                    'I' => 'Roboto-Regular.ttf',
                ],
                'days'=>[
                   // 'R' => 'days.ttf', // Обычный
                    'B' => 'days.ttf',    // Жирный (если есть)
                  //  'I' => 'days.ttf',    // Жирный (если есть)
                ]
            ]
        ];
        $lang=SiteHelper::GetLang();
        if ($lang=='ru' || $lang=='uk') {
            $params_mpdf['default_font'] = 'days';
        }else{
            $params_mpdf['default_font'] = 'roboto';
        }

        $contextOptions = [
            'http' => [
                'header' => "User-Agent: Mozilla/5.0\r\n"
            ]
        ];
        return $params_mpdf;
    }


}
