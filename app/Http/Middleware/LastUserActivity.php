<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Cache;
use Carbon\Carbon;


class LastUserActivity
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

        if(Auth::check()){
            $expiredAt = Carbon::now()->addMinutes(1);
            Cache::put('useronline'. Auth::user()->id, true, $expiredAt);
        }
        return $next($request);
            
    }
}
