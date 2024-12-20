<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\CommandHandlers;

use Illuminate\Support\Facades\Event;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;

readonly class ChangeInvoiceStatusHandler
{
    public function __construct(
        private GetInvoiceHandler          $getInvoiceHandler,
        private InvoiceRepositoryInterface $invoiceRepository
    )
    {
    }

    public function handle(ChangeInvoiceStatusCommand $command): void
    {
        $invoice = $this->getInvoiceHandler->handle(new GetInvoiceQuery($command->id));

        if (!$invoice) {
            throw new \InvalidArgumentException('Invoice not found.');
        }

        $invoice->tryChangeStatus($command->status);

        $this->invoiceRepository->save($invoice);

        foreach ($invoice->pullEvents() as $event) {
            Event::dispatch($event);
        }
    }
}
