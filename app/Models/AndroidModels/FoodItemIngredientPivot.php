<?php

namespace App\Models\AndroidModels;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FoodItemIngredientPivot extends Pivot
{
    protected $casts = [
        'quantity' => 'float'
    ];
}
