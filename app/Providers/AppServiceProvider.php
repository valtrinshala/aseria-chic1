<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('price', function ($expression){
            return "<?php
                list(\$price, \$settings) = [$expression];
                \$formattedPrice = number_format(\$price, 2, '.', ',');
                if (!\$settings->currency_symbol_on_left) {
                    echo \$formattedPrice . ' ' . \$settings->currency_symbol;
                } else {
                    echo \$settings->currency_symbol . ' ' . \$formattedPrice;
                }
            ?>";
        });
    }
}
