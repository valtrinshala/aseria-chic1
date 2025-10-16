<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $user = auth()->user();
        if ($user->role_id == config('constants.role.adminId')) {
            return redirect()->route('dashboard');
//        }elseif($user->role_id == config('constants.role.orderTakerId')){
//            return redirect()->route('pos.index');
//        }elseif ($user->role_id == config('constants.role.chefId')){
//            return redirect()->route('kitchen.index');
//        }elseif (count($user->userRole->permissions) !== 0){
        }else{
            foreach ($user->userRole->permissions as $key => $permission){
                if (in_array($permission, array_keys(Helpers::redirectUrl()))){
                    return redirect(url(Helpers::redirectUrl()[$user->userRole->permissions[$key]]));
                }
            }
            return redirect()->route('dashboard');
        }
//        return redirect()->route('dashboard');
    }
}
