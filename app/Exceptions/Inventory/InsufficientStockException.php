<?php

namespace App\Exceptions\Inventory;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use \Symfony\Component\HttpFoundation\Response as HttpFoundation;

class InsufficientStockException extends Exception
{
    public function __construct(
        public int $productId,
        public float $requestedQuantity,
        public float $availableQuantity
    )
    {
        $message = sprintf(
            'Estoque insuficiente para o produto (ID: %d). Disponível: %f, Solicitado: %f',
            $this->productId,
            $this->availableQuantity,
            $this->requestedQuantity
        );

        parent::__construct(
            message: $message,
            code: HttpFoundation::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getAvailableQuantity(): float
    {
        return $this->availableQuantity;
    }

    public function getRequestedQuantity(): float
    {
        return $this->requestedQuantity;
    }

    public function report(): void
    {
        Log::warning('Exception Estoque Insuficiente', [
            'product_id' => $this->productId,
            'available' => $this->availableQuantity,
            'requested' => $this->requestedQuantity
        ]);
    }


    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => 'INSUFFICIENT_STOCK',
            'product_id' => $this->productId,
            'available_quantity' => $this->availableQuantity,
            'required_quantity' => $this->requestedQuantity
        ], $this->getCode());
    }
}
