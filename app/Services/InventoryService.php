<?php

namespace App\Services;

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
}