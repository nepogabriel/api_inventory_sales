<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            throw new ModelNotFoundException("Produto não encontrado no estoque: {$productId}");
        
        return $inventory;
    }

    public function decrementQuantity(Inventory $inventory, int $quantity): void
    {
        if ($inventory->quantity < $quantity)
            throw new \RuntimeException("Estoque insuficiente para o produto: {$inventory->product_id}");
        
        $inventory->decrement('quantity', $quantity);
    }
}