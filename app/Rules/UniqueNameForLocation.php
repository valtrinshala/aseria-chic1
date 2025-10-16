<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class UniqueNameForLocation implements ValidationRule
{
    protected $model;

    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = $this->model::where($attribute, $value)->exists();

        if ($exists) {
            $fail(__('The :attribute must be unique within the specified location.'));
        }
    }
}
