<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Application\Product\Commands\ProductCommandHandler;
use Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductCommandHandler $handler,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['name', 'min_stock', 'sort', 'direction']);

        $items = $this->products->paginate($page, $perPage, $filters);
        $total = $this->products->count($filters);

        return response()->json([
            'data' => ProductResource::collection($items),
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->products->findById($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json(['data' => new ProductResource($product)]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->handler->create($request->validated());
        return response()->json(['data' => new ProductResource($product)], 201);
    }

    public function update(ProductRequest $request, int $id): JsonResponse
    {
        $product = $this->handler->update($id, $request->validated());
        return response()->json(['data' => new ProductResource($product)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->handler->delete($id);
        return response()->json(null, 204);
    }
}
