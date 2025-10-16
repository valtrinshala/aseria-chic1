<?php

namespace App\Traits;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait LocationScopeAndSortByNameTraitAndroid
{
    protected static function booted()
    {
        $user = auth()->user();
        static::addGlobalScope('location', function (Builder $builder) use ($user) {
            $builder->where('location_id', $user->location_id)
                ->orderByRaw("CONVERT(SUBSTRING_INDEX(name, ' ', -1), UNSIGNED) ASC")
                ->orderByRaw("SUBSTRING_INDEX(name, ' ', 1) ASC")
                ->orWhere('location_id', null);
        });
    }
}
