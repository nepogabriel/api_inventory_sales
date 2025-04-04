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

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required',
            'last_updated' => 'nullable|date',
        ]);

        $this->inventoryService->save($validated);

        return response()->json(Response::HTTP_CREATED);
    }
}
