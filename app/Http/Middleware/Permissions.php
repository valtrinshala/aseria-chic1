<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user()->userRole;
        $permissions = $user->permissions;
        if ($user->id == config('constants.role.adminId')) {
            return $next($request);
        } elseif (in_array($permission, $permissions)) {
            return $next($request);
        }else{
            if ($request->expectsJson()){
                return response()->json([
                    'status' => 1,
                    'message' => __('You do not have permissions to continue in this part, contact the administrator to enable you to have authorization to continue'),
                    'data' => null,
                    'redirect_uri' => null,
                ], 200);
            }else{
//                return redirect()->back()->withErrors(['pos_error' => __('You do not have permissions to continue in this part, contact the administrator to enable you to have authorization to continue')]);
                abort(401);
            }
        }
    }
}
