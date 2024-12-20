<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Notifications\Application\Events\ResourceSentSuccessfullyEvent;

readonly class InvoiceSentSuccessfullySubscriber
{
    public function handle(ResourceSentSuccessfullyEvent $event): void
    {
        Bus::dispatch(
            new ChangeInvoiceStatusCommand(
                id: new InvoiceId($event->resourceId),
                status: StatusEnum::Sending
            )
        );
    }
}
