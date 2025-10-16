<?php

namespace App\Traits;

use App\Models\Location;

trait CreatingLocationForEveryModelTrait
{
    public static function boot()
    {
        parent::boot();
        $location = Location::get();
        $user = auth()->user();
        if (count($location) <= 1) {
            self::creating(function ($model) {
                $model->location_id = Location::first()?->id;
            });
        } else {
            if ($user) {
                self::creating(function ($model) use ($user, $location) {
                    if ($user->role_id == config('constants.role.adminId')) {
                        if (!auth()->guard('web')->user()){
                            $model->location_id = Location::first()->id;
                        }else{
                            $model->location_id = session()->get('localization_for_changes_data')['id'] ?? $location->first()->id;
                        }
                    } else {
                        $model->location_id = $user->location_id;
                    }
                });
            }

        }
    }

}
