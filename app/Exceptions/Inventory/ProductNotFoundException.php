<?php

namespace App\Exceptions\Inventory;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use \Symfony\Component\HttpFoundation\Response as HttpFoundation;

class ProductNotFoundException extends Exception
{
    public function __construct(public int $productId)
    {
        parent::__construct(
            message: "Produto não encontrado no estoque.",
            code: HttpFoundation::HTTP_NOT_FOUND
        );
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function report(): void
    {
        Log::warning("Tentativa de acesso a produto não existente", [
            'product_id' => $this->productId,
            'request' => request()->all()
        ]);
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error_code' => 'PRODUCT_NOT_FOUND',
            'message' => $this->getMessage(),
            'product_id' => $this->productId,
        ], $this->getCode());
    }
}
