<?php

namespace App\Repositories;

use App\Models\SaleItem;

class SaleItemRepository
{
    public function save(array $data): bool
    {
        return SaleItem::insert($data);
    }
}