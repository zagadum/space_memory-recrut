<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Region;

class DictonaryController extends Controller {
    // Get Region By
    public function GetRegionByCountry($country_id)
    {
        $country_id = (int)$country_id;
        $jsonRet = [];
        if ($country_id > 0) {
            $jsonRet = Region::where('country_id', $country_id)->select('id', 'name')->get()->toArray();;
        }
        if (empty($jsonRet)) {
            $jsonRet = [];
        }
        return response()->json($jsonRet);
    }

    // Get Region By
    public function GetCityByRegion($region_id)
    {
        $region_id = (int)$region_id;
        $jsonRet = [];
        if ($region_id > 0) {
            $jsonRet = City::where('region_id', $region_id)->select('id', 'name')->get()->toArray();
        }
        if (empty($jsonRet)) {
            $jsonRet = [];
        }
        return response()->json($jsonRet);
    }
}
