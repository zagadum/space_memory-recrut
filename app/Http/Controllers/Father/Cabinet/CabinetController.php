<?php

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;

class CabinetController extends Controller
{
    public function index()
    {
        return view('father.cabinet');
    }
}