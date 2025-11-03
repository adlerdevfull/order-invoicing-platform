<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Invoice\Entities\Invoice;
use Domain\Invoice\Enums\TaxType;
use Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use Domain\Order\ValueObjects\Money;
use Infrastructure\Persistence\Models\InvoiceModel;

final class EloquentInvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice
    {
        $model = InvoiceModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findByOrderId(int $orderId): ?Invoice
    {
        $model = InvoiceModel::where('order_id', $orderId)->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = InvoiceModel::query();

        if (isset($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        return $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function count(array $filters = []): int
    {
        return InvoiceModel::query()->count();
    }

    public function save(Invoice $invoice): Invoice
    {
        $model = $invoice->id
            ? InvoiceModel::findOrFail($invoice->id)
            : new InvoiceModel();

        $model->fill([
            'order_id' => $invoice->orderId,
            'number' => $invoice->number,
            'identification_key' => $invoice->identificationKey,
            'tax_type' => $invoice->taxType->value,
            'net_amount' => $invoice->netAmount->amount,
            'tax_amount' => $invoice->taxAmount->amount,
            'total_amount' => $invoice->totalAmount->amount,
            'digital_signature' => $invoice->digitalSignature,
            'issued_at' => $invoice->issuedAt,
        ]);
        $model->save();

        $invoice->id = $model->id;
        return $invoice;
    }

    private function toDomain(InvoiceModel $model): Invoice
    {
        return new Invoice(
            id: $model->id,
            orderId: $model->order_id,
            number: $model->number,
            identificationKey: $model->identification_key,
            taxType: $model->tax_type,
            netAmount: new Money($model->net_amount),
            taxAmount: new Money($model->tax_amount),
            totalAmount: new Money($model->total_amount),
            digitalSignature: $model->digital_signature,
            issuedAt: $model->issued_at,
        );
    }
}
