<?php

namespace App\Http\Controllers;

use App\Exceptions\Inventory\InsufficientStockException;
use App\Exceptions\Inventory\ProductNotFoundException;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {}

    public function getSale(Sale $sale)
    {
        try {

            $sale->load(['items.product']);

            $detailsSale = $this->saleService->formatSale($sale);

            return response()->json([
                'data' => $detailsSale
            ]);

        } catch (ModelNotFoundException $e) {

            Log::warning('Algo deu errado ao registrar a venda.', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'error' => 'Venda não encontrada na base de dados'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            Log::warning('Algo deu errado ao registrar a venda.', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'error' => 'Ops! Tivemos um problema ao buscar os detalhes da venda.',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    public function createSale(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate([
                'items' => 'required',
                'items.*.product_id' => 'required|integer',
                'items.*.quantity' => 'required|numeric|min:0.001',
                'items.*.unit_price' => 'required|numeric|min:0.001',
                'items.*.unit_cost' => 'required|numeric|min:0.001',
            ]);

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
            Log::warning('Algo deu errado ao registrar a venda.', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);

            return response()->json([
                'message' => 'Algo deu errado ao registrar a venda.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
