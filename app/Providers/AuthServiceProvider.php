<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
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
            echo "Token Auth";
            if ($request->input('token')) {
                return User::where('token', $request->input('token'))->first();
            }
        });

        $this->app['auth']->viaRequest('user', function ($request) {
            echo "User Auth";
            if ($request->input('email') && $request->input('password')) {
                if ($user = User::where(['email' => $request->input('email')])->first()) {
                    if (Hash::check($request->input('password'), $user->password)) {
                        return $user;
                    }
                }
            }
            return null;
        });
    }
}
