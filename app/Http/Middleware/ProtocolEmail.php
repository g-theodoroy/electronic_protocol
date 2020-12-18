<?php

namespace App\Http\Middleware;

use Closure;
use App\Config;
use Auth;

class ProtocolEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // αν η βιβλιοθήκη imap δεν είναι φορτωμένη δεν προχωράω
        if (!extension_loaded('imap')) {
            return back();
        }
        // αν ο λογαριασμός είναι κενός δεν προχωράω
        $defaultImapEmail = Config::getConfigValueOf('defaultImapEmail');
        if (!$defaultImapEmail) {
            return back();
        }
        // αν οι χρήστες δεν επιτρέπεται να πρωτοκολλήσουν email
        $allowedEmailUsers = Config::getConfigValueOf('allowedEmailUsers');
        if ($allowedEmailUsers && strpos($allowedEmailUsers, Auth::user()->username) === false) {
            return back();
        }
        return $next($request);
    }
}
