<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeviceID
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $deviceID = $request->header('Device-ID');

        $device = "sw-152";

        if ($device != $deviceID && !$request->ajax()) {
            return response()->json(['message' => 'Pajisja nuk është e autorizuar.'], 401);
        }

        return $next($request);
    }
}
