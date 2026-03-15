<?php

namespace App\Http\Traits\AdminAuth;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use \App\Mail\Notification;
trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {

        $isOk=0;
        $user_id=0;
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        $loginStatus=$this->attemptLogin($request);


        if ($loginStatus['success']==1) {
            $request->session()->regenerate();

            $this->guard=$loginStatus['role'];
            $redirectHome='/';


            if ($loginStatus['role']=='student'){
                $redirectHome='/student';
                $studentGuard=$this->guard_student()->user();
                if(isset($studentGuard) ) {
                    if ( $studentGuard->enabled==1 && empty($studentGuard->deleted) && empty($studentGuard->blocked)){
                        $studentGuard->rollApiKey();
                        $request->session()->put('role', 'student');
                        $this->authenticated($request,$studentGuard);
                        $user_id=$studentGuard->id;
                        $studentGuard->sess_id=Session::getId();
                        $studentGuard->save();

                        $isOk=1;
                    }else{

                        if ($studentGuard->enabled==0){
                            $request->error='disable_account';
                        }
                        if ( $studentGuard->deleted==1){
                            $request->error='deleted_account';
                        }
                        if ($studentGuard->blocked==1) {
                            $request->error = 'blocked_account';
                            if (!empty($studentGuard->blocking_reason)){
                                $request->blocking_reason = $studentGuard->blocking_reason;
                            }
                        }
                        //------------ Уведомления о входе заблокированного пользователя
                            try {
                               $teacherEmail= $studentGuard->teacher->email;
                                $FranciseEmail= $studentGuard->franchisee->email;

                                $msgShow='';
                                if (isset($request->error) && $request->error=='disable_account'){
                                    $msgShow=trans('auth.disable_account');
                                }

                            if (isset($request->error) && $request->error=='blocked_account'){
                                $msgShow=trans('auth.blocked_account');
                                if (!empty($request->blocking_reason)){
                                    $msgShow=$msgShow.' ('.$request->blocking_reason.')';
                                }
                            }
                            $appDomain=config('app.locale', 'uk');
                            if (!empty($msgShow)){
                                if ($appDomain=='uk'){
                                    $data = ['message' =>$msgShow];
                                    $subject=trans('mail.block_user') .' '.$studentGuard->email;

                                    Mail::to($teacherEmail)->send(new Notification($data, $subject));
                                    Mail::to($FranciseEmail)->send(new Notification($data, $subject));
                                }
                            }

                        }catch (\Throwable $e) {

                            \Log::error('Failed to send login notification emails: '.$e->getMessage());
                        }
                        $loginStatus['role']='guest';
                        Auth::guard('recruting_student')->logout();
                    }
                }
            }




            if  ($isOk==1){
                session()->put('_login_touch', time());
                session()->save();
                Auth::shouldUse($loginStatus['role']);


                $this->clearLoginAttempts($request);
                return redirect()->intended($redirectHome);
            }else{
               // return $request->wantsJson() ? new Response('', 204) : redirect()->intended($redirectHome);
            }


            //return $request->wantsJson() ? new Response('', 204) : redirect()->intended($redirectHome);//$this->redirectPath()

        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $isOk=0;
        $isFranchisee=0;
        $isTeasher=0;
        $isManager=0;

        $isAdmin=0;
        $role='guest';

        $loginEmail=$this->credentials($request);
        $loginEmail['deleted']=0;

        $loginEmailStudent=$loginEmail;

        $isStudent=Auth::guard('recruting_student')->attempt($loginEmailStudent, $request->filled('remember'));
       if ($isStudent==1){
           $role='student';

       }

        return ['success' => $isOk, 'role' => $role];

    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {

    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if(isset($user) && Schema::hasColumn($user->getTable(), 'last_login_at')) {
            $user->last_login_at = now();
            $user->save();
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {


        $msgShow=trans('auth.failed');
        if (isset($request->error) && $request->error=='deleted_account'){
            $msgShow=trans('auth.deleted_account');
        }
        if (isset($request->error) && $request->error=='disable_account'){
            $msgShow=trans('auth.disable_account');
        }

        if (isset($request->error) && $request->error=='blocked_account'){
            $msgShow=trans('auth.blocked_account');
            if (!empty($request->blocking_reason)){
                $msgShow=$msgShow.' ('.$request->blocking_reason.')';
            }
        }

        throw ValidationException::withMessages([$this->username() => [ $msgShow]]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $this->guard_student()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson() ? new Response('', 204) : redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
            die('ok logout');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

}
