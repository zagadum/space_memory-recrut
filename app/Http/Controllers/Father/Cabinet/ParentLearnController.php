<?php

declare(strict_types=1);

namespace App\Http\Controllers\Father\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\RecrutingStudent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParentLearnController extends Controller
{
    public function index(Request $request): View
    {
        /** @var RecrutingStudent|null $student */
        $student = auth()->guard('recruting_student')->user();

        return view('father.learn', compact('student'));
    }
}

