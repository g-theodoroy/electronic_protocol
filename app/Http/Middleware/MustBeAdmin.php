<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeAdmin
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

        if ( $user->role->role == 'Διαχειριστής'){
        return $next($request);
        }
        return redirect($url);

    }

}
