<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Api\Events\InvoiceSendRequestEvent;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Application\Commands\SendInvoiceCommand;
use Modules\Invoices\Application\Dtos\InvoiceDto;
use Modules\Invoices\Application\EventSubscribers\InvoiceCreationRequestSubscriber;
use Modules\Invoices\Application\EventSubscribers\InvoiceSendRequestSubscriber;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class InvoiceSendRequestSubscriberTest extends TestCase
{
    public function testHandleDispatchesCreateInvoiceCommand(): void
    {
        $id = InvoiceId::new();
        $event = new InvoiceSendRequestEvent($id);

        Bus::fake();

        $subscriber = new InvoiceSendRequestSubscriber();
        $subscriber->handle($event);

        Bus::assertDispatched(SendInvoiceCommand::class, function (SendInvoiceCommand $command) use ($id) {
            return $command->id === $id;
        });
    }
}
