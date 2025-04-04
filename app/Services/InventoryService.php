<?php

namespace App\Services;

use App\Repositories\InventoryRepository;

class InventoryService
{
    public function __construct(
        private InventoryRepository $inventoryRepository
    ) {}

    public function save(array $data): void
    {
        $this->inventoryRepository->save($data);
    }
}