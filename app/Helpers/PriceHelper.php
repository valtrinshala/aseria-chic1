<?php

namespace App\Helpers;

class PriceHelper
{
    public static function formatPrice($price, $settings)
    {
        $formattedPrice = number_format($price, 2, '.', ',');
        if (!$settings->currency_symbol_on_left) {
            return $formattedPrice . ' ' . $settings->currency_symbol;
        } else {
            return $settings->currency_symbol . ' ' . $formattedPrice;
        }
    }
}
