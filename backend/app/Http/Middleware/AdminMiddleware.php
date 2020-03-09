<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminMiddleware
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
        $user = Auth::user();

        if ($user->role_id != config('constants.USER.ROLE.ADMIN')) {
            return new JsonResponse(['message' => 'Bạn phải là Admin mới có quyền thực hiện thao tác này'], 403);
        }

        return $next($request);
    }
}
