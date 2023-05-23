<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;

class RedirectAfterLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        // μετά από login
        if (basename(request()->headers->get('referer')) == 'login') {

            $landingPage = config('landing-page.page.' . auth()->user()->role_id);
            
            if($landingPage !== RouteServiceProvider::HOME){
                return redirect($landingPage);
            }
        }
        
        return $next($request);
    }
}
