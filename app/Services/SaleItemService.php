<?php

namespace App\Services;

use App\Models\SaleItem;

class SaleItemService
{
    public function __construct(
        private FormatterService $formatterService
    ) {}

    public static function formatSaleItem(SaleItem $item): array
    {
        return [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'unit_price' => FormatterService::formatMoney($item->unit_price),
            'unit_cost' => FormatterService::formatMoney($item->unit_cost),
            'subtotal' => FormatterService::formatMoney($item->unit_price * $item->quantity),
            'product' => ProductService::formatProduct($item->product)
        ];
    }
}