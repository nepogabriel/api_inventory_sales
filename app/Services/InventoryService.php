<?php

namespace App\Services;

use App\Exceptions\Inventory\InsufficientStockException;
use App\Repositories\InventoryRepository;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    public function __construct(
        private InventoryRepository $inventoryRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->inventoryRepository->getAll();
    }

    public function save(array $data): void
    {
        $this->inventoryRepository->save($data);
    }

    public function checkInventory(array $items): void
    {
        foreach ($items as $item) {
            $inventory = $this->inventoryRepository->findByProductId($item['product_id']);

            if ((float) $inventory->quantity < (float) $item['quantity'])
                throw new InsufficientStockException(
                    $item['product_id'],
                    $item['quantity'],
                    $inventory->quantity
                );
        }
    }
}