<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;

class IpLocationService
{

    /*
    |--------------------------------------------------------------------------
    | IpLocationService
    |--------------------------------------------------------------------------
    |
        The service checks the visitor's country, and if it doesn't match the subdomain, it displays a modal window asking the
        visitor to switch to a subdomain appropriate for their language or stay. After the selection, the setLocation
        cookie is saved. Format Cookie Value is ShortCountry:(0 or 1 not stay or stay if button stay on this domain was clicked)
    |
    */

    public $ipDomain = "";
    public $urlDomain="";
    public $stay_domain="";
    public $change_domain="";

    public $aText = array();

    public function check(Request $request)
    {

        $this->ipDomain = $this->getIpCountryDomain($request);

        $showSetLocationModal = $this->checkLocation($request);
        $this->stay_domain = config('redirect.' .$this->urlDomain );
        if ($this->ipDomain=="ua") $this->change_domain = config('redirect.uk');
        else  $this->change_domain = config('redirect.' . $this->ipDomain);


        $this->setText();
        return $showSetLocationModal;
    }

    public function checkLocation(Request $request){

        $showSetLocationModal = 0;
        $this->urlDomain = $this->getUrlDomain($request);

        if ( $request->hasCookie('setLocation')){

            $showSetLocationModal = 0;

        }else{
            $showSetLocationModal = 1;
             if ($this->urlDomain==$this->ipDomain){
                $showSetLocationModal = 0;
            }
        }
        $showSetLocationModal=0;
        return $showSetLocationModal;
    }

    public function getUrlDomain (Request $request){


        //return  config('app.locale');
        $domains = config('redirect');
        $url = request()->getHost();
        $key = array_search($url, $domains, true);

        if ($key=="uk") $key="ua";
        return $key;
    }

    public function getIpCountryDomain(Request $request){
        $ip = $request->ip();
        $countryCode = "en";

        // Ip's for Test
        //$ip = "79.110.200.27";  // pl
        //$ip ="109.162.90.128";  // ua
        //$ip ="102.165.52.0"; //de
        //$ip = "176.199.210.21";
       //$ip = "103.175.99.0";

        $data = Cache::remember("ipinfo_{$ip}", (24*3600), function() use ($ip) {
            return Http::withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])->get("https://ipapi.co/{$ip}/json/")->json();
        });

        if (!isset($data['error'])){
            if (isset($data['country_code'])){
                $countryCode = strtolower($data['country_code']);
            }
        }

        if ( $countryCode!="ua" && $countryCode!="pl")  $countryCode="en";

       // $countryCode = "pl";
        return $countryCode;
    }


    public function setText( ){

        $landname = $this->ipDomain;
        if ($this->ipDomain=="ua") $landname = "uk";

        $en_addition = "";

        if ($this->ipDomain=="en"){
            if ($this->urlDomain=="pl") $en_addition = "_pl";
            elseif ($this->urlDomain=="ua") $en_addition = "_ua";
        }


        $this->aText["question_modal_window_1"] = Lang::get('auth.question_modal_window_1', [], $landname)  ;

        $this->aText["question_modal_window_2"] = Lang::get('auth.question_modal_window_2' . $en_addition, [], $landname)  ;
        $this->aText["stayon_button"] = Lang::get('auth.stayon_button', [], $landname);
        $this->aText["change_platform_button"] = Lang::get('auth.change_platform_button', [], $landname);
        $this->aText["stayon_button_pl"] = Lang::get('auth.stayon_button', [], "pl");
        $this->aText["stayon_button_ua"] = Lang::get('auth.stayon_button', [], "uk");
        $this->aText["stayon_button_en"] = Lang::get('auth.stayon_button', [], "en");
    }

}
