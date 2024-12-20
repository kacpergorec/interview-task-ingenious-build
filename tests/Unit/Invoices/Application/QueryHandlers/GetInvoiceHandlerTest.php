<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Application\QueryHandlers;


use Modules\Invoices\Application\QueryHandlers\GetInvoiceHandler;
use PHPUnit\Framework\TestCase;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceQueryRepositoryInterface;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Application\Queries\GetInvoiceQuery;
use Symfony\Component\Uid\Uuid;
use Mockery;

class GetInvoiceHandlerTest extends TestCase
{
    public function testHandleReturnsInvoice(): void
    {
        $repositoryMock = Mockery::mock(InvoiceQueryRepositoryInterface::class);
        $invoiceId = InvoiceId::new();
        $invoice = Mockery::mock(Invoice::class);

        $repositoryMock->shouldReceive('find')
            ->with($invoiceId)
            ->once()
            ->andReturn($invoice);

        $handler = new GetInvoiceHandler($repositoryMock);

        $query = new GetInvoiceQuery($invoiceId);

        $result = $handler->handle($query);

        $this->assertSame($invoice, $result);
    }

    public function testHandleReturnsNullWhenInvoiceNotFound(): void
    {
        $repositoryMock = Mockery::mock(InvoiceQueryRepositoryInterface::class);
        $invoiceId = InvoiceId::new();

        $repositoryMock->shouldReceive('find')
            ->with($invoiceId)
            ->once()
            ->andReturn(null);

        $handler = new GetInvoiceHandler($repositoryMock);

        $query = new GetInvoiceQuery($invoiceId);

        $result = $handler->handle($query);

        $this->assertNull($result);
    }
}
