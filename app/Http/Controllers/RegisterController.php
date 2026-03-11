<?php

namespace App\Http\Controllers;
use App\Models\UsersInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Registered;
use  \App\Models\User;
use  \App\Models\Firms;
//use App\Http\Controllers\Adm\ValidationTrait;
 use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;
class RegisterController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guest:admin');
        $this->middleware('guest:franchisee');
        $this->middleware('guest:teacher');
        $this->middleware('guest:student');

    }
    protected function registered(Request $request)
    {
        die('registered');

        $req=$request->all();

        if ($request->method() == 'POST') {
            $req = $request->all();
            $validator = \Illuminate\Support\Facades\Validator::make($req, [
                'email' => 'required|email:rfc,dns|unique:users', 'password' => 'required|min:6|required_with:password_confirmation|same:password_confirmation','name'=>'required'
            ]);
            if (is_object($validator)) {
                $errors = $validator->errors();
            }
            if ($validator->fails()) {
                return redirect('/registered')->withErrors($validator)->withInput();
            }

            if (!empty($req['email']) && !empty($req['password'])){
                $user = $this->create($request->all());
                $isReg=new Registered($user);
                if($isReg) {
                    //Авто логинем
                    $user = User::find($user->id);
                    $user->status = '1';
                    $user->save();

                    Auth::login($user, true);
                    $type_profile=$user->type_profile;

                    if ($type_profile=='company'){ //регистрация по шагам компании
                        $firms=new Firms();
                        $firms->user_id=$user->id;
                        $firms->save();
                        return redirect()->route('register-firm');
                    }
                    if ($type_profile=='user'){
                        $userInfo=UsersInfo::where('user_id',$user->id)->first();
                        if (empty($userInfo['id'])){
                            $userInfo= new UsersInfo();
                            $userInfo->user_id=$user->id;
                            $userInfo->save();
                         }
                        return redirect()->route('register-user');
                    }
                    if ($type_profile=='volunteer'){ //регистрация по шагам компании
                        $userInfo=UsersInfo::where('user_id',$user->id)->first();
                        if (empty($userInfo['id'])){
                            $userInfo= new UsersInfo();
                            $userInfo->user_id=$user->id;
                            $userInfo->save();
                        }
                        return redirect()->route('register-volunteer');
                    }

                    Session::flash('ok', 'User successfully created.');
                } else {
                    Session::flash('error', 'Something went wrong, user wasn\'t successfully created.');
                }
            }

        }

        return view('auth.register');

    }



    protected function create(array $values) {
        if  (!in_array($values['type_profile'],['user','company','volunteer']))
        {
            $values['type_profile']='user';
        }

        return User::create([
            'email' => $values['email'],
            // 'title' => $values['title'],
            'name' => $values['name'],
            'user_name' => $values['name'],
            'tel' => $values['tel'],
            'type_profile' => $values['type_profile'],
            //'last_name' => $values['last_name'],
            'password' => bcrypt($values['password']),
            //'password_at' => strftime('%Y-%m-%d'),
            'status' => 1,
        ]);
    }


}

