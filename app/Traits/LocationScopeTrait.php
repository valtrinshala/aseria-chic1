<?php

namespace App\Traits;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait LocationScopeTrait
{
    protected static function booted()
    {
        $user = auth()->user();
        if ($user){
            if ($user->role_id == config('constants.role.adminId')) {
                if (session()?->get('localization_for_changes_data')){
                    static::addGlobalScope('location', function (Builder $builder) {
                        $builder->where('location_id', session()->get('localization_for_changes_data')['id'] ?? Location::first()->id)
                        ->orWhere('location_id', null);
                    });
                }
            } else {
                static::addGlobalScope('location', function (Builder $builder) use ($user) {
                    $builder->where('location_id', $user->location_id)
                    ->orWhere('location_id', null);
                });
            }
        }
    }
}
