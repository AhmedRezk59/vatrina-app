<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Vendor;

class CalculateProductsFinalPriceService
{
    public function calculate(Vendor $vendor)
    {
        $finalPrice = 0;

        $products = Product::whereIn(
            'id',
            Cart::query()
                ->whereBelongsTo($vendor)
                ->whereBelongsTo(auth('api')->user())
                ->select('id')
        )->get();

        foreach ($products as $product) {
            $finalPrice += $product->endPrice();
        }

        return $finalPrice;
    }
}