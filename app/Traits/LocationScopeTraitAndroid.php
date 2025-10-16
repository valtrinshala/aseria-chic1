<?php

namespace App\Traits;

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait LocationScopeTraitAndroid
{
    protected static function booted()
    {
        $user = auth()->user();
        static::addGlobalScope('location', function (Builder $builder) use ($user) {
            $builder->where('location_id', $user->location_id)
                ->orWhere('location_id', null);
        });
    }
}
