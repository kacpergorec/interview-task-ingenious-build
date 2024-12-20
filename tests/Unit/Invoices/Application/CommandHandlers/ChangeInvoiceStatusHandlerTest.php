<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Application\CommandHandlers;

use Illuminate\Support\Facades\Event;
use Modules\Invoices\Application\CommandHandlers\ChangeInvoiceStatusHandler;
use Modules\Invoices\Application\Commands\ChangeInvoiceStatusCommand;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Events\InvoiceSendingEvent;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Tests\TestCase;

class ChangeInvoiceStatusHandlerTest extends TestCase
{
    public function testHandleSuccessfullyChangesInvoiceStatus(): void
    {
        $repositoryMock = $this->createMock(InvoiceRepositoryInterface::class);
        $getInvoiceHandlerMock = $this->createMock(GetInvoiceHandler::class);

        $invoiceId = InvoiceId::new();
        $invoice = Invoice::create($invoiceId, new CustomerInfo('General Grevious', '4armmaster@droidmail.xyz'));

        $getInvoiceHandlerMock->expects($this->once())
            ->method('handle')
            ->willReturn($invoice)
        ;

        Event::fake();

        $handler = new ChangeInvoiceStatusHandler($getInvoiceHandlerMock, $repositoryMock);
        $command = new ChangeInvoiceStatusCommand($invoiceId, StatusEnum::Sending);

        $handler->handle($command);

        $this->assertSame(StatusEnum::Sending, $invoice->getStatus());
        Event::assertDispatched(InvoiceSendingEvent::class);
    }

    public function testHandleThrowsExceptionWhenInvoiceNotFound(): void
    {
        $repositoryMock = $this->createMock(InvoiceRepositoryInterface::class);
        $getInvoiceHandlerMock = $this->createMock(GetInvoiceHandler::class);

        $getInvoiceHandlerMock->expects($this->once())
            ->method('handle')
            ->willReturn(null)
        ;

        $handler = new ChangeInvoiceStatusHandler($getInvoiceHandlerMock, $repositoryMock);
        $command = new ChangeInvoiceStatusCommand(InvoiceId::new(), StatusEnum::Sending);

        $this->expectException(\InvalidArgumentException::class);
        $handler->handle($command);
    }
}
