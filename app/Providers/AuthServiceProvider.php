<?php

namespace App\Providers;

 use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Gate;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Gate::define('admin.translation.index', function ($user) { return true; });
       // Gate::define('admin.translation.edit', function ($user) { return true; });
      //  Gate::define('admin.translation.rescan', function ($user) { return true; });

//        Gate::define('admin.post.index', function ($user) { return true; });
//        Gate::define('admin.post.create', function ($user) { return true; });
//        Gate::define('admin.post.edit', function ($user) { return true; });
//        Gate::define('admin.post.delete', function ($user) { return true; });
         //  Gate::define('admin.users.index', function ($user) { return true; });
        // Check for first name/last name/email part in the password
        Validator::extend('name_in_password', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();

            if(empty($data['email']) || empty($data['first_name']) || empty($data['last_name'])) {
                if(!empty($data['id'])) {
                    $user = User::where('id', '=', $data['id']);
                } else if (!empty($data['email'])) {
                    $user = User::where('email', '=', $data['email']);
                } else {
                    return true;
                }
                $data = $user->first();
            }

            $email_found = false;
            $first_name_found = false;
            $last_name_found = false;
            if (!empty($data['email'])) {
                $s_email = explode('@', $data['email']);
                $email_found = stripos($value, $s_email[0]);
            }
            if (!empty($data['first_name'])) {
                $first_name_found = stripos($value, $data['first_name']);
            }
            if (!empty($data['last_name'])) {
                $last_name_found = stripos($value, $data['last_name']);
            }

            if ($email_found !== false || $first_name_found !== false || $last_name_found !== false) {
                return false;
            }

            return true;
        });

        // Check for contiguous of same characters in the password
        Validator::extend('same_in_password', function ($attribute, $value, $parameters, $validator) {
            $value_arr = str_split($value);
            $same_count = 0; $prev_char = '';
            foreach($value_arr as $next_char) {
                if($next_char === $prev_char) {
                    $same_count++;
                }
                if($same_count == 2) return false;
                $prev_char = $next_char;
            }
            return true;
        });

        // Check for 3 previous passwords and current password for new password
        Validator::extend('prev_password', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            if(!empty($data['id'])) {
                $user = User::where('id', '=', $data['id']);
            } else if (!empty($data['email'])) {
                $user = User::where('email', '=', $data['email']);
            } else {
                return true;
            }
            $data = $user->first();

            if(!empty($data)) {
                $passwords = [
                    $data->password,
                    $data->prev1_password,
                    $data->prev2_password,
                    $data->prev3_password,
                ];

                foreach($passwords as $next_password) {
                    if(password_verify($value, $next_password))
                        return false;
                }
            } else {
                return true;
            }

            return true;
        });

        // check for OLD password in new password form
        Validator::extend('old_password', function ($attribute, $value, $parameters, $validator) {
            $env_data = $validator->getData();
            $user = User::find($env_data['id']);
            $user_data = $user->first();
            return password_verify($env_data['old_password'], $user_data->password);
        });


        // Check for password characters from at least 3 categories: A-Z/a-z/0-9/~!@#$%^*&;?+_.
        Validator::extend('cat_in_password', function ($attribute, $value, $parameters, $validator) {
            $cat_counter = 0;
            $cat_counter += preg_match('/[a-z]/', $value);
            $cat_counter += preg_match('/[A-Z]/', $value);
            $cat_counter += preg_match('/[0-9]/', $value);
            $cat_counter += preg_match('/[~!@#$%^*&;?+_.]/', $value);

            return $cat_counter >= 3
                ? true
                : false;
        });

    }
}
