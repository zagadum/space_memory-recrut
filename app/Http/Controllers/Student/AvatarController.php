<?php


namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;

class AvatarController extends Controller
{
    public function uploadAvatar(Request $request)
    {
        if (Auth::guard('recruting_student')->check()) {
            $this->studentObj = Auth::guard('recruting_student')->user();
            $request->validate(['photo' => 'required|image|mimes:jpeg,png,jpg,gif']);


            $avatar = $request->file('photo');
            $filename =  $this->studentObj->id. '_ava.png';

            $path = public_path('useruploads/users/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }

            Image::make($avatar)->orientate()->fit(128, 128)->save($path);
            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($path, true);
            }

            return response()->json(['photoUrl' => asset('useruploads/users/' . $filename)]);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}
