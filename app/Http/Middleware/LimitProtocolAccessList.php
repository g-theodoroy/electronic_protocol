<?php

namespace App\Http\Middleware;

use Closure;
use App\Config;
use Auth;

class LimitProtocolAccessList
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

        $user = $request->user();
        if ( Config::getConfigValueOf('limitProtocolAccessList') && in_array($user->role_description(), ["Συγγραφέας",  "Αναγνώστης"])){
            return redirect()->back();
        }

        return $next($request);
    }
}
