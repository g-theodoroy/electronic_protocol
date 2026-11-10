<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TouchUserActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Αν ο χρήστης είναι συνδεδεμένος, ανανέωσε το session timestamp του
        if (Auth::check()) {
            DB::table('sessions')
                ->where('user_id', Auth::id())
                ->update(['last_activity' => time()]);
        }

        return $next($request);
    }
}
