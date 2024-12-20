<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Application\EventSubscribers;

use Illuminate\Support\Facades\Bus;
use Modules\Invoices\Api\Events\InvoiceCreationRequestEvent;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Modules\Invoices\Application\Dtos\InvoiceDto;
use Modules\Invoices\Application\EventSubscribers\InvoiceCreationSubscriber;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class InvoiceCreationSubscriberTest extends TestCase
{
    public function testHandleDispatchesCreateInvoiceCommand(): void
    {
        $invoiceDto = new InvoiceDto(
            id: new InvoiceId(Uuid::v7()),
            customerInfo: new CustomerInfo('Han Solo', 'han.solo@korelia.xyz'),
            invoiceLines: []
        );

        $event = new InvoiceCreationRequestEvent($invoiceDto);

        Bus::fake();

        $subscriber = new InvoiceCreationSubscriber();
        $subscriber->handle($event);

        Bus::assertDispatched(CreateInvoiceCommand::class, function (CreateInvoiceCommand $command) use ($invoiceDto) {
            return $command->dto === $invoiceDto;
        });
    }
}
