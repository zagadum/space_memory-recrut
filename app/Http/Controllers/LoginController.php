<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller{



    public function login(){

        return redirect()->intended('/admin/login');
//        $user= Auth::user();
//        if (!empty($user->id)){
//            return redirect()->intended('/cabinet/home');
//
//        }else{
//            return view('auth.login');
//
//        }

    }
    public function auth(Request $request){

        die;
        $credentials = $request->only('email', 'password');
        $error=0;
        $errors=[];
        if ($request->method() == 'POST') {
            $req=$request->all();
            $validator = \Illuminate\Support\Facades\Validator::make($req, [
                'email' => 'required|email:rfc,dns', 'password' => 'required',
                ]);




            if (is_object($validator)) {
                $errors = $validator->errors();
            }
            if ($validator->fails()) {
                return redirect('/login')->withErrors($validator)->withInput();
            }

            if (Auth::attempt($credentials)) {

                Session::flash('ok', 'User successfully created.');
                // Authentication passed...
                return redirect()->intended('/cabinet/home');
                //return redirect()->intended('dashboard');
            }else{
                $error=1;
                //  print 'Error Login';die;
            }
        }



        return view('auth.login',['error'=>$error,'errors'=>$errors]);
    }


    public function logout(Request $request)
    {

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }



//if (Auth::check()) {  ---проверка авторизации


//use Illuminate\Support\Facades\Auth;
//// Retrieve the currently authenticated user...
//$user = Auth::user();

//// Retrieve the currently authenticated user's ID...
//$id = Auth::id();


//Вход
// f (Auth::attempt(['email' => $email, 'password' => $password, 'active' => 1])) {
// Authentication was successful...
//}
//---- Досутп к админ входу
//Имя охранника, переданное guardметоду, должно соответствовать одному из охранников, настроенных в вашем auth.phpфайле конфигурации:
//if (Auth::guard('admin')->attempt($credentials)) {
//    // ...
//}
//------ Запоминание пользователя
//Ваша usersтаблица должна включать строковый remember_tokenстолбец,
//if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
//    // The user is being remembered...
//}
//Auth::login($user); или Auth::login($user, $remember = true);

// авторизация по ид Auth::loginUsingId(1);
}

