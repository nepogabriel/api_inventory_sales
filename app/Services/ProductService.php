<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public static function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'cost_price' => FormatterService::formatMoney($product->cost_price),
            'sale_price' => FormatterService::formatMoney($product->sale_price)
        ];
    }
}