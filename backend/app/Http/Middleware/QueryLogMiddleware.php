<?php

namespace App\Http\Middleware;

use Closure;

class QueryLogMiddleware
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
        \DB::connection('mongodb')->enableQueryLog();

        $res = $next($request);

        \Log::info(json_encode(\DB::connection('mongodb')->getQueryLog()));

        return $res;
    }
}
