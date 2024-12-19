<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\CommandHandlers;

use Illuminate\Support\Facades\Event;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;

readonly class CreateInvoiceHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $repository
    )
    {
    }

    public function handle(CreateInvoiceCommand $command): void
    {
        $Dto = $command->dto;

        $invoice = Invoice::create(
            id: $Dto->id,
            customerInfo: $Dto->customerInfo,
        );

        foreach ($Dto->invoiceLines as $line) {
            $invoice->addInvoiceProductLine(
                $line->id,
                $line->name,
                $line->quantity,
                $line->price
            );
        }

        $this->repository->save($invoice);

        foreach ($invoice->pullEvents() as $event) {
            Event::dispatch($event);
        }
    }
}
