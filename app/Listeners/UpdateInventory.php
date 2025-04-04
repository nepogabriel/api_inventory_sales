<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Repositories\InventoryRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateInventory
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private InventoryRepository $inventoryRepository
    ) {}

    /**
     * Handle the event.
     */
    public function handle(SaleCreated $event): void
    {
        try {
            foreach ($event->sale->items as $item) {
                $inventory = $this->inventoryRepository->findByProductId($item->product_id);
                $this->inventoryRepository->decrementQuantity($inventory, $item->quantity);

                Log::info("Estoque atualizado para o produto: {$inventory->product_id}", [
                    'sale_id' => $event->sale->id,
                    'product_id' => $item->product_id,
                    'quantity_subtracted' => $item->quantity,
                    'updated_quantity' => $inventory->quantity
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Ocorreu uma falha ao atualizar o estoque para a venda: {$event->sale->id}", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
