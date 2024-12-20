<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Api\Events\InvoiceSendRequestEvent;
use Modules\Invoices\Application\CommandHandlers\ChangeInvoiceStatusHandler;
use Modules\Invoices\Application\CommandHandlers\CreateInvoiceHandler;
use Modules\Invoices\Application\CommandHandlers\SendInvoiceHandler;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Application\Commands\SendInvoiceCommand;
use Modules\Invoices\Application\EventSubscribers\InvoiceCreationRequestSubscriber;
use Modules\Invoices\Application\EventSubscribers\InvoiceDeliveredSubscriber;
use Modules\Invoices\Application\EventSubscribers\InvoiceSendRequestSubscriber;
use Modules\Invoices\Application\EventSubscribers\InvoiceSentSuccessfullySubscriber;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceQueryRepository;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceRepository;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Modules\Notifications\Application\Events\ResourceSentSuccessfullyEvent;

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
        Event::listen(InvoiceCreationRequestEvent::class, [InvoiceCreationRequestSubscriber::class, 'handle']);
        Event::listen(InvoiceSendRequestEvent::class, [InvoiceSendRequestSubscriber::class, 'handle']);
        Event::listen(ResourceSentSuccessfullyEvent::class, [InvoiceSentSuccessfullySubscriber::class, 'handle']);
        Event::listen(ResourceDeliveredEvent::class, [InvoiceDeliveredSubscriber::class, 'handle']);

        Bus::map([
            CreateInvoiceCommand::class => CreateInvoiceHandler::class,
            SendInvoiceCommand::class => SendInvoiceHandler::class,
            ChangeInvoiceStatusCommand::class => ChangeInvoiceStatusHandler::class,
        ]);
    }
}
