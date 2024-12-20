<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Application\EventSubscribers\InvoiceDeliveredSubscriber;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class InvoiceDeliveredSubscriberTest extends TestCase
{
    public function testHandleDispatchesCreateInvoiceCommand(): void
    {
        $event = new ResourceDeliveredEvent(Uuid::v7());

        Bus::fake();

        $subscriber = new InvoiceDeliveredSubscriber();
        $subscriber->handle($event);

        Bus::assertDispatched(ChangeInvoiceStatusCommand::class);
    }
}
