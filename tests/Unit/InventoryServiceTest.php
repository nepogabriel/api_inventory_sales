<?php

namespace Tests\Unit\Services;

use App\Exceptions\Inventory\InsufficientStockException;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use App\Services\InventoryService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class InventoryServiceTest extends TestCase
{
    private InventoryService $inventoryService;
    private InventoryRepository $inventoryRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->inventoryRepositoryMock = $this->createMock(InventoryRepository::class);
        
        $this->inventoryService = new InventoryService($this->inventoryRepositoryMock);
    }

    public function testGetAllReturnsCollection()
    {
        $expectedCollection = new Collection([
            new Inventory(['product_id' => 1, 'quantity' => 10]),
            new Inventory(['product_id' => 2, 'quantity' => 20]),
        ]);
        
        $this->inventoryRepositoryMock
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($expectedCollection);
        
        $result = $this->inventoryService->getAll();
        
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedCollection, $result);
    }

    public function testSaveDelegatesToRepository()
    {
        $testData = ['product_id' => 1, 'quantity' => 15];
        
        $this->inventoryRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($testData);
        
        $this->inventoryService->save($testData);
    }

    public function testCheckInventoryPassesWithSufficientStock()
    {
        $items = [
            ['product_id' => 1, 'quantity' => 5],
            ['product_id' => 2, 'quantity' => 10]
        ];
        
        $inventory1 = new Inventory(['product_id' => 1, 'quantity' => 10]);
        $inventory2 = new Inventory(['product_id' => 2, 'quantity' => 15]);
        
        $this->inventoryRepositoryMock
            ->method('findByProductId')
            ->willReturnMap([
                [1, $inventory1],
                [2, $inventory2]
            ]);
        
        $this->inventoryService->checkInventory($items);
        $this->assertTrue(true);
    }

    public function testCheckInventoryThrowsExceptionForInsufficientStock()
    {
        $items = [
            ['product_id' => 1, 'quantity' => 15]
        ];
        
        $inventory = new Inventory(['product_id' => 1, 'quantity' => 10]);
        
        $this->inventoryRepositoryMock
            ->method('findByProductId')
            ->with(1)
            ->willReturn($inventory);
        
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Estoque insuficiente para o produto (ID: 1). Disponível: 10.000000, Solicitado: 15.000000');
        
        $this->inventoryService->checkInventory($items);
    }

    public function testCheckInventoryThrowsExceptionAtFirstInsufficientItem()
    {
        $items = [
            ['product_id' => 1, 'quantity' => 5],
            ['product_id' => 2, 'quantity' => 20],
        ];
        
        $inventory1 = new Inventory(['product_id' => 1, 'quantity' => 10]);
        $inventory2 = new Inventory(['product_id' => 2, 'quantity' => 15]);
        
        $this->inventoryRepositoryMock
            ->method('findByProductId')
            ->willReturnMap([
                [1, $inventory1],
                [2, $inventory2]
            ]);
        
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Estoque insuficiente para o produto (ID: 2). Disponível: 15.000000, Solicitado: 20.000000');
        
        $this->inventoryService->checkInventory($items);
    }

    public function testCheckInventoryHandlesFloatQuantitiesCorrectly()
    {
        $items = [
            ['product_id' => 1, 'quantity' => 5.5]
        ];
        
        $inventory = new Inventory(['product_id' => 1, 'quantity' => 10.5]);
        
        $this->inventoryRepositoryMock
            ->method('findByProductId')
            ->with(1)
            ->willReturn($inventory);
        
        $this->inventoryService->checkInventory($items);
        $this->assertTrue(true);
    }

    public function testCheckInventoryThrowsExceptionForFloatQuantitiesWhenInsufficient()
    {
        $items = [
            ['product_id' => 1, 'quantity' => 10.6]
        ];
        
        $inventory = new Inventory(['product_id' => 1, 'quantity' => 10.5]);
        
        $this->inventoryRepositoryMock
            ->method('findByProductId')
            ->with(1)
            ->willReturn($inventory);
        
        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Estoque insuficiente para o produto (ID: 1). Disponível: 10.500000, Solicitado: 10.600000');
        
        $this->inventoryService->checkInventory($items);
    }
}