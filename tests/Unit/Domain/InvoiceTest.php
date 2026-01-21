<?php

declare(strict_types=1);

use Domain\Invoice\Entities\Invoice;
use Domain\Invoice\Enums\TaxType;
use Domain\Order\ValueObjects\Money;

test('generates invoice with correct tax calculation', function () {
    $invoice = Invoice::generate(1, new Money(10000), TaxType::General);

    expect($invoice->netAmount->amount)->toBe(10000);
    expect($invoice->taxAmount->amount)->toBe(2100); // 21%
    expect($invoice->totalAmount->amount)->toBe(12100);
    expect($invoice->number)->toStartWith('INV-');
    expect($invoice->digitalSignature)->not->toBeNull();
    expect(strlen($invoice->identificationKey))->toBe(32);
});

test('reduced tax rate applies correctly', function () {
    $invoice = Invoice::generate(1, new Money(10000), TaxType::Reduced);
    expect($invoice->taxAmount->amount)->toBe(1000); // 10%
});

test('super reduced tax rate applies correctly', function () {
    $invoice = Invoice::generate(1, new Money(10000), TaxType::SuperReduced);
    expect($invoice->taxAmount->amount)->toBe(400); // 4%
});
