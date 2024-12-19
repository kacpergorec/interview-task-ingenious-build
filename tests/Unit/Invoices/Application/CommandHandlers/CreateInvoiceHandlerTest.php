<?php
declare(strict_types=1);

namespace Tests\Unit\Invoices\Application\CommandHandlers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Modules\Invoices\Application\CommandHandlers\CreateInvoiceHandler;
use Modules\Invoices\Application\Dtos\InvoiceDto;
use Modules\Invoices\Application\Dtos\InvoiceProductLineDto;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Events\InvoiceCreatedEvent;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceRepository;
use Modules\Invoices\Application\Commands\CreateInvoiceCommand;
use Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;
use Tests\TestCase;

class CreateInvoiceHandlerTest extends TestCase
{
    public function testHandleCreatesInvoiceAndDispatchesEvents(): void
    {
        $repositoryMock = $this->createMock(InvoiceRepository::class);

        $invoiceId = new InvoiceId(Uuid::v7());
        $customerInfo = new CustomerInfo('Han Solo', 'han.solo@korelia.xyz');

        $lineId = new InvoiceProductLineId(Uuid::v7());
        $lineDto = new InvoiceProductLineDto(
            id: $lineId,
            quantity: 1,
            name: 'Millennium Falcon',
            price: new Money(100000000)
        );

        $invoiceDto = new InvoiceDto($invoiceId, $customerInfo, [$lineDto]);
        $command = new CreateInvoiceCommand($invoiceDto);

        $repositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Invoice::class));

        Event::fake();

        $handler = new CreateInvoiceHandler($repositoryMock);

        $handler->handle($command);

        Event::assertDispatched(InvoiceCreatedEvent::class);
    }

    public function testHandleThrowsExceptionIfSaveFails(): void
    {
        $repositoryMock = $this->createMock(InvoiceRepository::class);

        $repositoryMock
            ->expects($this->once())
            ->method('save')
            ->willThrowException(new \Exception('Database error'));

        $invoiceId = new InvoiceId(Uuid::v7());
        $customerInfo = new CustomerInfo('Han Solo', 'han.solo@korelia.xyz');

        $lineId = new InvoiceProductLineId(Uuid::v7());
        $lineDto = new InvoiceProductLineDto(
            id: $lineId,
            quantity: 1,
            name: 'Millennium Falcon',
            price: new Money(100000000)
        );
        $invoiceDto = new InvoiceDto($invoiceId, $customerInfo, [$lineDto]);

        $command = new CreateInvoiceCommand($invoiceDto);
        $handler = new CreateInvoiceHandler($repositoryMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $handler->handle($command);
    }
}
