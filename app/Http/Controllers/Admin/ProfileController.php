<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ChangeEmail;
use App\Models\Franchisee;
use App\Models\Student;
use App\Models\Teacher;


use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public $adminUser;

    /**
     * Guard used for admin user
     *
     * @var string
     */
    protected $guard = 'admin';

    public function __construct()
    {
        // TODO add authorization
         $this->guard = session('role');
    }

    /**
     * Get logged user before each method
     *
     * @param Request $request
     */
    protected function setUser($request)
    {
        $this->guard=$request->session()->get('role');

        if (empty($request->user($this->guard))) {
            abort(404, 'Admin User not found');
        }

        $this->adminUser = $request->user($this->guard);
    }

    /**
     * Show the form for editing logged user profile.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function editProfile(Request $request)
    {

        $this->setUser($request);

        return view('admin.profile.edit-profile', ['adminUser' => $this->adminUser,'role'=>$this->guard]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @throws ValidationException
     * @return array|RedirectResponse|Redirector
     */
    public function updateProfile(Request $request)
    {
        $this->setUser($request);
        $adminUser = $this->adminUser;

      //  dd($adminUser);

        // Validate the request
        $this->validate($request, [
            'first_name' => ['nullable', 'string'],
            'surname' => ['nullable', 'string'],
            'patronymic' => ['nullable', 'string'],
            'email' => ['sometimes', 'email', Rule::unique('admin_users', 'email')->ignore($this->adminUser->getKey(), $this->adminUser->getKeyName()), 'string'],
            'language' => ['sometimes', 'string'],

        ]);

        // Sanitize input
        $sanitized = $request->only([
            'first_name',
            'last_name',
            'email',
            'language',

        ]);
        $student =Student::where('email', $sanitized['email'])->first();
        $teacher =Teacher::where('email', $sanitized['email'])->first();
        $franchisee =Franchisee::where('email', $sanitized['email'])->first();

        if((!$student && !$teacher && !$franchisee) || $this->adminUser->email === $sanitized['email'] ) {
            // Update changed values AdminUser
            $this->adminUser->update($sanitized);

            $newEmail = (object)[];
            $newEmail->email = $sanitized['email'];
            $newEmail->name = $this->adminUser->first_name;
            Mail::to($newEmail->email)->send(new ChangeEmail($newEmail));

            if ($request->ajax()) {
                return ['redirect' => url('admin/profile'), 'message' => trans('admin.operation.succeeded')];
            }
        }
        else{
            return [ 'redirect' => url('admin/profile'), 'errorMessage' => trans('admin.operation.existed')];
        }
        return redirect('admin/profile');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function editPassword(Request $request)
    {
        $this->setUser($request);

        return view('admin.profile.edit-password', [
            'adminUser' => $this->adminUser,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @throws ValidationException
     * @return array|RedirectResponse|Redirector
     */
    public function updatePassword(Request $request)
    {

        $this->setUser($request);
        $adminUser = $this->adminUser;

        // Validate the request
        $this->validate($request, [
            'password' => ['sometimes', 'confirmed', 'min:7', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],

        ]);

        // Sanitize input
        $sanitized = $request->only([
            'password',

        ]);

        $password =$sanitized['password'];

        //Modify input, set hashed password
        $sanitized['password'] = Hash::make($sanitized['password']);

        // Update changed values AdminUser
        $this->adminUser->update($sanitized);

        $email = $request->get('email');
        $newPassword = (object)[];
        $newPassword->email =$email;
        $newPassword->password = $password;
        $newPassword->name = $this->adminUser->first_name;
        Mail::to($newPassword->email)->send(new ChangeEmail($newPassword));

        if ($request->ajax()) {
            return ['redirect' => url('admin/password'), 'message' => trans('admin.operation.succeeded')];
        }

        return redirect('admin/password');
    }
}
