<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\Region\IndexRegion;

use Illuminate\Contracts\View\Factory;

use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;
use App\Models\Video;
class LearnController  extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexRegion $request
     * @return array|Factory|View
     */
    public function index(IndexRegion $request)
    {


       //$user=Auth::guard('admin')->user()->id;
        $video=Video::all();

        return view('admin.learning.index', ['video' => $video]);
    }



}
