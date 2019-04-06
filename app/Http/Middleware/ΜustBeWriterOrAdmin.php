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

        if ( in_array ( $user->role->role, ['Διαχειριστής', 'Αναθέτων', 'Συγγραφέας'])){
        return $next($request);
        }
        return redirect($url);
    }
}
