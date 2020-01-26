<?php

namespace App\Providers;

use App\User;
use App\Admin;
use App\Goal;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {

                return User::where('api_token', $request->input('api_token'))->first();

            }elseif ($request->header('Authorization')) {
                $token = $this->formatToken($request->header('Authorization'));
                    $user_check = User::where('api_token', $token)->first();
                    $admin_check = Admin::where('api_token', $token)->first();
                    if ($user_check) {
                        return $user_check;
                    }else if ($admin_check) {
                        return $admin_check;
                    }
            }
        });

        // Gate::define('control', function (User $user, Goal $goal) {
        //     return $user->id === $goal->owner_id;
        // });
       
    }
    
    public function formatToken($bearer) {
        //exploade to an array
        $explode_bearer = explode(" ", $bearer);
        return $token = $explode_bearer[1];
    }
}
