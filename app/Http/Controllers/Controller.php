<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Api-s for ekiosk",
 *     version="1.0.0"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Provides default settings for all controllers
     * extended by controller
     *
     * @return object
     */
    protected function master(): object
    {
        return Setting::find(1);
    }

    protected function isAdmin(){
        $user = auth()->user();
        if ($user->role_id == config('constants.role.adminId')){
            return true;
        }else{
            return false;
        }
    }

    protected function calculateAmountFromModel($modelClass, $key, $duration = 'month', $isLast = false)
    {
        return $modelClass::where(['is_cancelled' => false, 'is_paid' => true])->duration($duration, $isLast)->sum($key);
    }

    public function currentLocation()
    {
        $user = auth()->user();
        if ($user->role_id == config('constants.role.adminId')){
            if (!auth()->guard('web')->user()){
                $location = Location::first();
            }else{
                $location = session()->get('localization_for_changes_data') ?? Location::first();
            }
        }else{
            $location = $user->location;
        }
        return $location;
    }

}
