<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseModel;
use App\Models\AndroidModels\Kitchen;
use App\Models\AndroidModels\Pos;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $entity): Response
    {
        $deviceToken = $request->header('Device-Token');

        if (!$deviceToken) {
            return ResponseModel::error('Device token is required', 400);
        }

        $accessToken = PersonalAccessToken::findToken($deviceToken);

        if (!$accessToken || ($accessToken->expires_at && $accessToken->expires_at->isPast())) {
            return ResponseModel::error('Invalid or expired device token', 403);
        }

        $deviceModelMap = [
            'pos' => Pos::class,
            'kitchen' => Kitchen::class,
        ];

        if (!array_key_exists($entity, $deviceModelMap)) {
            return ResponseModel::error("Invalid entity type: {$entity}", 400);
        }

        $device = $deviceModelMap[$entity]::where('status', true)->find($accessToken->tokenable_id);

        if (!$device) {
            return ResponseModel::error("No {$entity} found, {$entity} has been deactivated, please contact the administrator!", 404);
        }

        $request->merge(['kitchenDevice' => $device]);

        return $next($request);
    }
}
