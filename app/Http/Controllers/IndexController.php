<?php

namespace App\Http\Controllers;


use App\Helpers\SiteHelper;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller{


    public function ResetCache(){
        \Cache::flush();
        print 'Clear:Cache';
    }
    public function FirstPage(){
        //-- Кеширование новостей
        if (isset($_GET["cookies_agree"])) {
//            ob_clean();
            setcookie("cookies_agree", 1, time()+3600000000, "/");
            header("Location:/");
            die();
        }

        $lang=SiteHelper::GetLang();
        if ($lang=='ru' or $lang=='ru_ru'){$prefix='';}else{$prefix='_'.$lang;}
        $key = 'blog_main';
        $blog = \Cache::get($key);
        if($blog === null) {
            $blog=News::where('status',1)->where('type_news','blog')->select('id','name'.$prefix.' as title','img_src','descr_shot_'.$lang.' as descr_shot','friendly_url','date_add')->orderBy('date_add','desc')->limit(4)->get();
            \Cache::put($key, $blog, 900);
        }
        return view('first-page',['blog'=>$blog]);
    }
}
