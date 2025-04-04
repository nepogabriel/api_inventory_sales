<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    public function getAll()
    {
        $inventory = $this->inventoryService->getAll();

        return response()->json($inventory, Response::HTTP_OK);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
            'last_updated' => 'nullable|date',
        ]);

        $this->inventoryService->save($validated);

        return response()->json(Response::HTTP_CREATED);
    }
}
