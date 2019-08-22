<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Factory as Auth;

class AdminOnlyMiddleware
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
         $permit = $request->header('Permit');
         $value = DB::table('admins')
                        ->where('verify_code', $permit)
                        ->exists();
         if (!$value) {
           return response()->json(['message' => 'Forbidden'], 403);
         }
          return $next($request);
      }

}