<?php

namespace App\Http\Middleware;

use Closure;

class ΜustBeWriterOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $url)
    {
        $user = $request->user();

        if ( $user->role->role == 'Διαχειριστής' or $user->role->role == 'Συγγραφέας'){
        return $next($request);
        }
        return redirect($url);
    }
}
