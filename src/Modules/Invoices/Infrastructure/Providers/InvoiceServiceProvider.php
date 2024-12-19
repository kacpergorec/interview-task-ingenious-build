<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceQueryRepository;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            InvoiceQueryRepositoryInterface::class,
            InvoiceQueryRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
