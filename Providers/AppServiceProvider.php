<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Add this custom validation rule alphabets and space.
        \Validator::extend('alpha_spaces', function ($attribute, $value) {
            // This will only accept alpha dot and spaces. 
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s.]+$/u', $value);
        });

        // password validation
        \Validator::extend('password', function ($attribute, $value) {
            // one upper case, one lower case, one digit[0-9], 
            // one special character[#?@$^&-_] and the minimum length should be 8.
            return preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?@$^&-_]).{7,}\S+$/', $value);
        });

        // password validation
        \Validator::extend('valid_email', function ($attribute, $value) {
            // one upper case, one lower case, one digit[0-9], 
            // one special character[#?@$^&-_] and the minimum length should be 8.
            return (filter_var($value, FILTER_VALIDATE_EMAIL));
        });           
        
        \Redis::enableEvents();
        
    }

}
