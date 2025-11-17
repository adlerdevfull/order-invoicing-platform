<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use Application\Invoice\Commands\GenerateInvoiceHandler;
use Domain\Invoice\Enums\TaxType;
use Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly GenerateInvoiceHandler $generateHandler,
        private readonly InvoiceRepositoryInterface $invoices,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $filters = $request->only(['order_id']);

        $items = $this->invoices->paginate($page, $perPage, $filters);
        $total = $this->invoices->count($filters);

        return response()->json([
            'data' => InvoiceResource::collection($items),
            'meta' => ['total' => $total, 'page' => $page, 'per_page' => $perPage],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoices->findById($id);
        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }
        return response()->json(['data' => new InvoiceResource($invoice)]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|integer',
            'tax_type' => 'sometimes|string|in:general,reduced,super_reduced',
        ]);

        $taxType = TaxType::from($request->input('tax_type', 'general'));
        $invoice = $this->generateHandler->handle($request->input('order_id'), $taxType);

        return response()->json(['data' => new InvoiceResource($invoice)], 201);
    }
}
