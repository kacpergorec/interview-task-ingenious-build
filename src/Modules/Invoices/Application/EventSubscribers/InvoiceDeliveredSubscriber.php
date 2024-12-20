<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

readonly class InvoiceDeliveredSubscriber
{

    public function handle(ResourceDeliveredEvent $event): void
    {
        Bus::dispatch(
            new ChangeInvoiceStatusCommand(
                id: new InvoiceId($event->resourceId),
                status: StatusEnum::SentToClient
            )
        );
    }
}
