<?php

namespace App\Services;

use App\Helpers\ResponseModel;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutDevicesUsersService
{
    public function logout($token){
        $tokenModel = PersonalAccessToken::findToken($token);
        if ($tokenModel) {
            $tokenModel->update(['expires_at' => now()]);
            return ResponseModel::success(["message" => "Logged out successfully"]);
        }
        return ResponseModel::error(__('Invalid token'), 401);
    }
}
