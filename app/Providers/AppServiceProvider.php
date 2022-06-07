<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\User;
use App\Config;
use Illuminate\Mail\Mailer;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        error_reporting(E_ALL ^ E_NOTICE);
        try {
            View::share('myActiveUsers', User::my_active_users());
            View::share('myUsers', User::my_users());
            View::share('myUsers', User::my_users());

            $ipiresiasName = Config::getConfigValueOf('ipiresiasName');
            View::share('ipiresiasName', $ipiresiasName);
            $titleColor = Config::getConfigValueOf('titleColor');
            $titleColorStyle = '';
            if ($titleColor) {
                $titleColorStyle = "style='background:" . $titleColor . "'" ;
            }
            View::share('titleColorStyle', $titleColorStyle);
            $defaultImapEmail = Config::getConfigValueOf('defaultImapEmail');
            if (! extension_loaded('imap')) {
                $defaultImapEmail = null;
            }
            View::share('defaultImapEmail', $defaultImapEmail);

            $allowedEmailUsers =  Config::getConfigValueOf('allowedEmailUsers');
            View::share('allowedEmailUsers', $allowedEmailUsers);

            $wideListProtocol = Config::getConfigValueOf('wideListProtocol');
            View::share('wideListProtocol', $wideListProtocol);

        } catch (\Throwable $e) {
            report($e);
            // καμία ενέργεια απλά πιάνει το λάθος
        // γιατί χτύπαγε στη δημιουργία των πινάκων με php artisan:migrate
        }
    }
}
