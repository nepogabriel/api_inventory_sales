<?php

namespace App\Services;

use App\Events\SaleCreated;
use App\Models\Sale;
use App\Repositories\SaleItemRepository;
use App\Repositories\SaleRepository;
use Illuminate\Support\Facades\Log;

class SaleService
{
    public function __construct(
        private SaleRepository $saleRepository,
        private SaleItemRepository $saleItemRepository,
        private InventoryService $inventoryService
    ) {}

    public function createSale(array $data): void
    {
        try {
            $totals = $this->calculateTotals($data['items']);

            $sale = $this->saveSale($totals);

            $this->saveSaleItems($data['items'], $sale->id);

            SaleCreated::dispatch($sale);
        } catch (\Exception $e) {
            Log::error(
                'Algo deu errado ao registrar a venda na base de dados.',
                ['error' => $e->getMessage()]
            );

            throw $e;
        }
    }

    private function saveSale(array $totals): Sale
    {
        return $this->saleRepository->save([
            'total_amount' => $totals['total_amount'],
            'total_cost' => $totals['total_cost'],
            'total_profit' => $totals['total_profit'],
            'status' => 'completed',
        ]);
    }

    private function saveSaleItems(array $items, $saleId): void
    {
        $saleItems = [];
        $now = now();

        foreach ($items as $item) {
            $saleItems[] = [
                'sale_id' => $saleId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'unit_cost' => $item['unit_cost'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->saleItemRepository->save($saleItems);
    }

    private function calculateTotals(array $items): array
    {
        $totalAmount = 0;
        $totalCost = 0;
        
        foreach ($items as $item) {
            $totalAmount += $item['unit_price'] * $item['quantity'];
            $totalCost += $item['unit_cost'] * $item['quantity'];
        }
        
        return [
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'total_profit' => $totalAmount - $totalCost,
        ];
    }
}