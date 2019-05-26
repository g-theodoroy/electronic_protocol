<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\User;
use App\Config;

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
	error_reporting(E_ALL ^ E_NOTICE);
      try{
        View::share('myActiveUsers', User::my_active_users());
        View::share('myUsers', User::my_users());
        View::share('myUsers', User::my_users());

        $config = new Config;
        $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
        View::share('ipiresiasName', $ipiresiasName);
        $titleColor = $config->getConfigValueOf('titleColor');
        $titleColorStyle = '';
        if($titleColor) $titleColorStyle = "style='background:" . $titleColor . "'" ;
        View::share('titleColorStyle', $titleColorStyle);
      } catch (\Exception $e) {
        // καμία ενέργεια απλά πιάνει το λάθος
        // γιατί χτύπαγε στη δημιουργία των πινάκων με php artisan:migrate
      }

    }
}
