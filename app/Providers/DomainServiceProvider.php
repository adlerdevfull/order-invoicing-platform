<?php

declare(strict_types=1);

namespace App\Providers;

use Domain\Invoice\Repositories\InvoiceRepositoryInterface;
use Domain\Order\Repositories\OrderRepositoryInterface;
use Domain\Product\Repositories\ProductRepositoryInterface;
use Infrastructure\Persistence\Repositories\{
    EloquentInvoiceRepository,
    EloquentOrderRepository,
    EloquentProductRepository
};
use Application\Order\Commands\{CreateOrderHandler, TransitionOrderHandler};
use Application\Product\Commands\ProductCommandHandler;
use Application\Invoice\Commands\GenerateInvoiceHandler;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, EloquentInvoiceRepository::class);

        // Command handlers
        $this->app->bind(CreateOrderHandler::class);
        $this->app->bind(TransitionOrderHandler::class);
        $this->app->bind(ProductCommandHandler::class);
        $this->app->bind(GenerateInvoiceHandler::class);
    }
}
