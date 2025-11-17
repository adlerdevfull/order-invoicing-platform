<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use Application\Order\Commands\{CreateOrderCommand, CreateOrderHandler, TransitionOrderHandler};
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Repositories\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly CreateOrderHandler $createHandler,
        private readonly TransitionOrderHandler $transitionHandler,
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['status', 'user_id', 'sort', 'direction']);

        $items = $this->orders->paginate($page, $perPage, $filters);
        $total = $this->orders->count($filters);

        return response()->json([
            'data' => OrderResource::collection($items),
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->orders->findById($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        return response()->json(['data' => new OrderResource($order)]);
    }

    public function store(CreateOrderRequest $request): JsonResponse
    {
        $command = new CreateOrderCommand(
            userId: auth('api')->id(),
            items: $request->validated('items'),
            discountCents: $request->validated('discount_cents', 0),
        );

        $order = $this->createHandler->handle($command);

        return response()->json(['data' => new OrderResource($order)], 201);
    }

    public function transition(Request $request, int $id): JsonResponse
    {
        $request->validate(['status' => 'required|string']);

        $status = OrderStatus::from($request->input('status'));
        $order = $this->transitionHandler->handle($id, $status);

        return response()->json(['data' => new OrderResource($order)]);
    }
}
