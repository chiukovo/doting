<?php

namespace App\Http\Middleware;

use Closure;

class WebAuth
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
        if (isWebLogin()) {
            return $next($request);
        } else {
            return redirect('/');
        }
    }
}
