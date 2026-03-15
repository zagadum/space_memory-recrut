<?php

namespace App\Http\Controllers\Auth;

use  App\Http\Traits\AdminAuth\AuthenticatesUsers;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Where to redirect users after logout.
     *
     * @var string
     */
    protected $redirectToAfterLogout = '/admin/login';

    /**
     * Guard used for admin user
     *
     * @var string
     */
    protected $guard = 'admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){

        $this->guard = config('admin-auth.defaults.guard');
        $this->redirectTo = config('admin-auth.login_redirect');
        $this->redirectToAfterLogout = config('admin-auth.logout_redirect');

        $this->middleware('guest')->except('logout');

        $this->middleware('guest:student')->except('logout');


    }

    private function ChekRule($role=null){
            if (isset($role)) {
                if (Auth::guard($role)->check()) {
                    $userObj = Auth::guard($role)->user();
                    if ($userObj->id>0){
                        if ($role=='student'){
                            if (empty($userObj->blocked) && empty($userObj->deleted)){
                                return true;
                            }
                        }
                    }
                }
            }
            return false;
        }
    /**
     * Показывает форму входа
     */
    public function showLoginForm(Request $request){
        $role = session('role');


        if (isset($role) && !$this->ChekRule($role)) {
            return $this->logout(request());
        }

        if (isset($role)){
            if ($role=='student'){
                return redirect('/test-parent-portal');
            }

        }


 
     
        return view('auth.login' );
    }

   
    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request) {

        $this->guard()->logout();

        $this->guard_student()->logout();//zaga


        $request->session()->flush();

        $request->session()->regenerate();

        return redirect($this->redirectToAfterLogout);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
        $conditions = [];
        if (config('admin-auth.check_forbidden')) {
            $conditions['forbidden'] = false;
        }
        if (config('admin-auth.activation_enabled')) {
            $conditions['activated'] = true;
        }

        return array_merge($request->only($this->username(), 'password'), $conditions);
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectAfterLogoutPath(): string
    {
        if (method_exists($this, 'redirectToAfterLogout')) {
            return $this->redirectToAfterLogout();
        }

        return property_exists($this, 'redirectToAfterLogout') ? $this->redirectToAfterLogout : '/';
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return StatefulGuard
     */
    protected function guard() //admin
    {
        return Auth::guard($this->guard);
    }
    protected function guard_student()
    {
        return Auth::guard('recruting_student');
    }


}
