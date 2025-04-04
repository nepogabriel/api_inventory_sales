<?php

namespace App\Http\Controllers;

use App\Exceptions\Inventory\InsufficientStockException;
use App\Exceptions\Inventory\ProductNotFoundException;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {}

    public function getSale(Sale $sale)
    {
        $sale->load(['items.product']);
        $formattedSale = $this->saleService->formatSale($sale);

        return response()->json([
            'data' => $formattedSale
        ]);
    }

    public function createSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0.001',
        ]);

        try {

            $this->saleService->createSale($validated);
            
            return response()->json([
                'message' => 'Venda registrada com sucesso!'
            ], Response::HTTP_CREATED);

        } catch (InsufficientStockException $e) {

            $response = json_decode($e->render($request)->content(), true);
            return response()->json($response, $e->getCode());

        } catch (ProductNotFoundException $e) {

            $response = json_decode($e->render($request)->content(), true);
            return response()->json($response, $e->getCode());

        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Algo deu errado ao registrar a venda.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
