<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
//        var_dump(Auth::guard($guard)->check());
//        var_dump(Auth::id());
//        var_dump(User::find(Auth::id())->isAdmin());
//        var_dump(Auth::check());
        //dd(auth()->user()->isAdmin());
        if (Auth::guard($guard)->check() && auth()->user()->isActive() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        return redirect('/admin/login');
//        return route('sabc-login-get');
    }
}
