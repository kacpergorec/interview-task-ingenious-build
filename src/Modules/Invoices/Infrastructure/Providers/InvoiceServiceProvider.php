<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Application\CommandHandlers\CreateInvoiceHandler;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Application\EventSubscribers\InvoiceCreationSubscriber;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceQueryRepository;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceRepository;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(InvoiceQueryRepositoryInterface::class, InvoiceQueryRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(
            InvoiceCreationRequestEvent::class,
            [InvoiceCreationSubscriber::class, 'handle']
        );

        Bus::map([CreateInvoiceCommand::class => CreateInvoiceHandler::class,]);
    }
}
