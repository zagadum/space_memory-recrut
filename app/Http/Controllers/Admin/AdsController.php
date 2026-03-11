<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\Ads\IndexAds;

use App\Http\Requests\Admin\Ads\UpdateAds;
use App\Models\Ads;
use App\AdminModule\AdminListing;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class AdsController extends Controller
{
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $role = session('role', '');
            if (!in_array($role, ['admin', 'franchisee'])) {
                abort(403, 'Доступ запрещен');
            }
            return $next($request);
        });
    }

    public function index(IndexAds $request){
        $data = AdminListing::create(Ads::class)->processRequestAndGet($request, ['id', 'url', 'img', 'enabled'], []);

         if (!empty($data)) {
             foreach ($data as &$items) {
                 $items->img =  $items->img;
             }
         }
        return view('admin.ads.index', ['data' => $data]);
    }

    public function edit(Ads $ads,$adsId) {
        $ads = Ads::find($adsId);
        return view('admin.ads.edit', ['ads' => $ads]);
    }

    public function upload(UpdateAds $request,  $id)
    {
        $success=false;
        $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,gif']);
        $fileBaner = $request->file('photo');
        $url='';
        if ($fileBaner) {
            $ads = Ads::find($id);

            if (!empty($ads->img) && file_exists($ads->img)) {
                @unlink($ads->img);
            }
            $filename =  $ads->id. time().'_rkl.png';
            $path = public_path('useruploads/news/' . $filename);
            Image::make($fileBaner)->orientate()->fit(237, 148)->save($path);


            $url =  '/useruploads/news/' . $filename;
            $ads->update(['img' =>$url]);
            $success = true;
        }
        return response()->json(['photoUrl'=>$url,'success'=>$success]);

    }
    public function update(UpdateAds $request,  $id)
    {
        $sanitized = $request->getSanitized();

        $sanitized['enabled'] = $sanitized['enabled'] ? 1 : 0;
        $ads = Ads::find($id);

        $ads->update($sanitized);
        if ($request->ajax()) {
            return [
                'redirect' => url('admin/ads'),
                'message' => trans('admin.operation.succeeded'),
            ];
        }
       return redirect('admin/ads');
    }


}
