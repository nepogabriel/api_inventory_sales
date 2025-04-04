<?php

namespace Tests\Unit\Services;

use App\Events\SaleCreated;
use App\Models\Sale;
use App\Repositories\SaleItemRepository;
use App\Repositories\SaleRepository;
use App\Services\FormatterService;
use App\Services\InventoryService;
use App\Services\SaleItemService;
use App\Services\SaleService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    private $saleRepository;
    private $saleItemRepository;
    private $inventoryService;
    private $saleService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saleRepository = $this->createMock(SaleRepository::class);
        $this->saleItemRepository = $this->createMock(SaleItemRepository::class);
        $this->inventoryService = $this->createMock(InventoryService::class);

        $this->saleService = new SaleService(
            $this->saleRepository,
            $this->saleItemRepository,
            $this->inventoryService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_sale_successfully()
    {
        // Configurar o Event fake para testar se foi disparado
        Event::fake();

        // Dados de entrada
        $saleData = [
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price' => 10.50,
                    'unit_cost' => 5.25,
                ],
                [
                    'product_id' => 2,
                    'quantity' => 1,
                    'unit_price' => 20.00,
                    'unit_cost' => 10.00,
                ],
            ],
        ];

        // Mock do InventoryService
        $this->inventoryService->expects($this->once())
            ->method('checkInventory')
            ->with($saleData['items']);

        // Mock do SaleRepository
        $expectedTotals = [
            'total_amount' => 41.00,
            'total_cost' => 20.50,
            'total_profit' => 20.50,
        ];

        $saleModel = new Sale();
        $saleModel->id = 123;

        $this->saleRepository->expects($this->once())
            ->method('save')
            ->with([
                'total_amount' => $expectedTotals['total_amount'],
                'total_cost' => $expectedTotals['total_cost'],
                'total_profit' => $expectedTotals['total_profit'],
                'status' => 'completed',
            ])
            ->willReturn($saleModel);

        // Mock do SaleItemRepository
        $this->saleItemRepository->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($items) use ($saleModel) {
                return count($items) === 2 &&
                       $items[0]['sale_id'] === $saleModel->id &&
                       $items[1]['sale_id'] === $saleModel->id;
            }));

        // Execução
        $this->saleService->createSale($saleData);

        // Verificação do evento
        Event::assertDispatched(SaleCreated::class, function ($event) use ($saleModel) {
            return $event->sale->id === $saleModel->id;
        });
    }

    public function test_create_sale_throws_exception_when_inventory_check_fails()
    {
        $saleData = [
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_price' => 10.50,
                    'unit_cost' => 5.25,
                ],
            ],
        ];

        $this->inventoryService->expects($this->once())
            ->method('checkInventory')
            ->willThrowException(new \Exception('Inventory check failed'));

        $this->saleRepository->expects($this->never())
            ->method('save');

        $this->saleItemRepository->expects($this->never())
            ->method('save');

        Log::shouldReceive('error')
            ->once()
            ->with(
                'Algo deu errado ao registrar a venda na base de dados.',
                ['error' => 'Inventory check failed']
            );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Inventory check failed');

        $this->saleService->createSale($saleData);
    }

    public function test_calculate_totals_correctly()
    {
        $items = [
            [
                'product_id' => 1,
                'quantity' => 2,
                'unit_price' => 10.00,
                'unit_cost' => 5.00,
            ],
            [
                'product_id' => 2,
                'quantity' => 3,
                'unit_price' => 15.00,
                'unit_cost' => 7.50,
            ],
        ];

        $result = $this->saleService->calculateTotals($items);

        $this->assertEquals(65.00, $result['total_amount']); // (2*10 + 3*15)
        $this->assertEquals(32.50, $result['total_cost']);   // (2*5 + 3*7.5)
        $this->assertEquals(32.50, $result['total_profit']); // (65 - 32.5)
    }
}