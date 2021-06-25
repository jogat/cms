<?php

namespace App\Http\Middleware;

use Closure;

class AuthRequiredMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if (auth()->active()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response('Unauthorized. Please login.', 401);
        }

        return redirect('/login?next=' . $request->getRequestUri());

    }
}
