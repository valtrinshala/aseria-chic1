<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAbility
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $ability): Response
    {
        if (!$request->user() || !$request->user()->tokenCan($ability)) {
            return ResponseModel::error('Unauthorized', 401);
        }

        return $next($request);
    }
}
