<?php

namespace App\Http\Middleware;

use Closure;

class IsAdminOne
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
        if(Auth()->user()->isAdminOne() or Auth()->user()->isAdminTwo()){
            return $next($request);
        }else{
            return response()->json([
                'message' => 'unauthorized',
            ],401);
        }

    }
}
