<?php

declare(strict_types=1);

namespace App\Http\Controllers\Father;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearnController extends Controller
{
    public function index(Request $request): View
    {
        return view('father.learn');
    }
}
