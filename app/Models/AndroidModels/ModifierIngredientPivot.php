<?php

namespace App\Models\AndroidModels;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ModifierIngredientPivot extends Pivot
{
    protected $casts = [
        'quantity' => 'float'
    ];
}
