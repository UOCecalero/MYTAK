<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;

class SelfUser
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
        
        $auth = Auth::user();
        $user = User::find($request->user);

        if ($auth->id != $user->id ){ 

            return response(" ", 401)
                  ->header('Content-Type', 'text/plain');
            }

         return $next($request);

        
    }
}
