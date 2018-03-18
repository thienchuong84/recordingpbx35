<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckUserIsActive
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
        // if user is not active, or disabled
        if( Auth::user()->is_active == 0 )
        {
            Auth::logout();
            return redirect('not_active');
        }
        return $next($request);
    }
}
