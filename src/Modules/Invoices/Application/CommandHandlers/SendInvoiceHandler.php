<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\CommandHandlers;


use Illuminate\Support\Facades\Event;
use Modules\Invoices\Application\Commands\SendInvoiceCommand;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;

readonly class SendInvoiceHandler
{
    public function __construct(
        private GetInvoiceHandler           $getInvoiceHandler,
        private NotificationFacadeInterface $notificationFacade
    )
    {
    }

    public function handle(SendInvoiceCommand $command): void
    {
        $invoice = $this->getInvoiceHandler->handle(
            new GetInvoiceQuery($command->id)
        );

        if (!$invoice) {
            throw new \InvalidArgumentException('Invoice not found.');
        }

        $invoice->validateProductLines();

        $notifyData = new NotifyData(
            resourceId: $invoice->id->toUuid(),
            toEmail: $invoice->customerInfo->email,
            subject: 'Invoice Notification',
            message: 'Hi, '. $invoice->customerInfo->name . '! Your invoice is ready to be viewed.',
        );

        $this->notificationFacade->notify($notifyData);

        foreach ($invoice->pullEvents() as $event) {
            Event::dispatch($event);
        }
    }
}
