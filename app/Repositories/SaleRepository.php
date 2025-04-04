<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository
{
    public function save(array $data): Sale
    {
        return Sale::create($data);
    }
}