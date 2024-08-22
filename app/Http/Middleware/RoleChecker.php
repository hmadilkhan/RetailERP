<?php

namespace App\Http\Middleware;

use Closure;

class RoleChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //        $roles = Auth::check() ? Auth::user()->role->pluck('name')->toArray() : [];

        if (auth()->user()->role == 1) {
            return $next($request);
        }

        return redirect('/dashboard');
    }
}
