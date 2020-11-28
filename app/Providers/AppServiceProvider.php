<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\User;
use App\Config;
use Swift_SmtpTransport;
use Swift_Mailer;
use Illuminate\Mail\Mailer;

;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //https://laravel-news.com/allowing-users-to-send-email-with-their-own-smtp-settings-in-laravel
        $this->app->bind('user.mailer', function ($app, $parameters) {
            $smtp_host = array_get($parameters, 'smtp_host');
            $smtp_port = array_get($parameters, 'smtp_port');
            $smtp_username = array_get($parameters, 'smtp_username');
            $smtp_password = array_get($parameters, 'smtp_password');
            $smtp_encryption = array_get($parameters, 'smtp_encryption');

            $from_email = array_get($parameters, 'from_email');
            $from_name = array_get($parameters, 'from_name');

            $from_email = $parameters['from_email'];
            $from_name = $parameters['from_name'];

            $transport = new Swift_SmtpTransport($smtp_host, $smtp_port);
            $transport->setUsername($smtp_username);
            $transport->setPassword($smtp_password);
            $transport->setEncryption($smtp_encryption);

            $swift_mailer = new Swift_Mailer($transport);

            $mailer = new Mailer($app->get('view'), $swift_mailer, $app->get('events'));
            $mailer->alwaysFrom($from_email, $from_name);
            $mailer->alwaysReplyTo($from_email, $from_name);

            return $mailer;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        error_reporting(E_ALL ^ E_NOTICE);
        try {
            View::share('myActiveUsers', User::my_active_users());
            View::share('myUsers', User::my_users());
            View::share('myUsers', User::my_users());

            $config = new Config;
            $ipiresiasName = $config->getConfigValueOf('ipiresiasName');
            View::share('ipiresiasName', $ipiresiasName);
            $titleColor = $config->getConfigValueOf('titleColor');
            $titleColorStyle = '';
            if ($titleColor) {
                $titleColorStyle = "style='background:" . $titleColor . "'" ;
            }
            View::share('titleColorStyle', $titleColorStyle);
            $defaultImapEmail = $config->getConfigValueOf('defaultImapEmail');
            if (! extension_loaded('imap')) {
                $defaultImapEmail = null;
            }
            View::share('defaultImapEmail', $defaultImapEmail);
        } catch (\Exception $e) {
            // καμία ενέργεια απλά πιάνει το λάθος
        // γιατί χτύπαγε στη δημιουργία των πινάκων με php artisan:migrate
        }
    }
}
