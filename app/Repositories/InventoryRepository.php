<?php

namespace App\Repositories;

use App\Exceptions\Inventory\ProductNotFoundException;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryRepository
{
    public function getAll(): Collection
    {
        $inventory = Inventory::all();
        return $inventory;
    }

    public function save(array $data): void
    {
        Inventory::updateOrCreate(
            ['product_id' => $data['product_id']],
            [
                'quantity' => DB::raw("quantity + {$data['quantity']}"),
                'last_updated' => $data['last_updated'] ?? now(),
            ]
        );
    }

    public function findByProductId(int $productId): Inventory
    {
        $inventory = Inventory::where('product_id', $productId)->first();
        
        if (!$inventory)
            throw new ProductNotFoundException($productId);
        
        return $inventory;
    }

    public function decrementQuantity(Inventory $inventory, int $quantity): void
    {
        $inventory->decrement('quantity', $quantity);
    }
}