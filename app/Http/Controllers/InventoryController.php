<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function getAll()
    {
        try {

            $inventory = $this->inventoryService->getAll();

            return response()->json($inventory, Response::HTTP_OK);

        } catch (\Exception $e) {

            Log::warning('Ops! Tivemos um problema ao buscar o estoque.', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'error' => 'Ops! Tivemos um problema ao buscar o estoque.',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function save(Request $request)
    {
        try {

            $validated = $request->validate([
                'product_id' => 'required',
                'quantity' => 'required',
                'last_updated' => 'nullable|date',
            ]);
    
            $this->inventoryService->save($validated);
    
            return response()->json(Response::HTTP_CREATED);

        } catch (\Exception $e) {

            Log::warning('Ops! Tivemos um problema ao registrar o estoque.', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'error' => 'Ops! Tivemos um problema ao registrar o estoque.',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
