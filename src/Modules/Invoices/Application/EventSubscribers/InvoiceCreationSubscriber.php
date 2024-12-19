<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;

readonly class InvoiceCreationSubscriber
{
    public function handle(InvoiceCreationRequestEvent $event): void
    {
        Bus::dispatch(
            new CreateInvoiceCommand($event->invoiceDto)
        );
    }
}
