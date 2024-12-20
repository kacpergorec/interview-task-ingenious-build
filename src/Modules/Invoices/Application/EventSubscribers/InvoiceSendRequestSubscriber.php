<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Api\Events\InvoiceSendRequestEvent;
use Modules\Invoices\Application\Commands\SendInvoiceCommand;

readonly class InvoiceSendRequestSubscriber
{
    public function handle(InvoiceSendRequestEvent $event): void
    {
        Bus::dispatch(
            new SendInvoiceCommand($event->invoiceId)
        );
    }
}
