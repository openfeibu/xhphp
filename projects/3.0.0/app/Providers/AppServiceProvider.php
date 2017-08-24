<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('identitycards', function($attribute, $value, $parameters){
            return preg_match('/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/', $value);
        });
        Validator::extend('mobile', function($attribute, $value, $parameters){
            return preg_match('/^0?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/', $value);
        });
        Validator::extend('limit', function($attribute, $value, $parameters){
            $length = get_str_length($value);
            if($length < $parameters[0] || $length > $parameters[0])
            {
                return false;
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
