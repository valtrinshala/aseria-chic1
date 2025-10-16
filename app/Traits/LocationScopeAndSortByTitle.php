<?php

namespace App\Traits;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;

trait LocationScopeAndSortByTitle
{
    protected static function booted()
    {
        $user = auth()->user();
        if ($user){
            if ($user->role_id == config('constants.role.adminId')) {
                if (session()?->get('localization_for_changes_data')){
                    static::addGlobalScope('location', function (Builder $builder) {
                        $builder->where('location_id', session()->get('localization_for_changes_data')['id'])
                            ->orderByRaw("CONVERT(SUBSTRING_INDEX(title, ' ', -1), UNSIGNED) ASC")
                            ->orderByRaw("SUBSTRING_INDEX(title, ' ', 1) ASC")
                            ->orWhere('location_id', null);
                    });
                }else {
                    static::addGlobalScope('location', function (Builder $builder) {
                        $builder->orderByRaw("CONVERT(SUBSTRING_INDEX(title, ' ', -1), UNSIGNED) ASC")
                            ->orderByRaw("SUBSTRING_INDEX(title, ' ', 1) ASC")
                            ->orWhere('location_id', null);
                    });
                }
            } else {
                static::addGlobalScope('location', function (Builder $builder) use ($user) {
                    $builder->where('location_id', $user->location_id)
                        ->orderByRaw("CONVERT(SUBSTRING_INDEX(title, ' ', -1), UNSIGNED) ASC")
                        ->orderByRaw("SUBSTRING_INDEX(title, ' ', 1) ASC")
                        ->orWhere('location_id', null);
                });
            }
        }
    }
}
