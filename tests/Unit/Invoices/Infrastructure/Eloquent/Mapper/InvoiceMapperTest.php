<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Infrastructure\Eloquent\Mapper;

use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceProductLineModel;
use PHPUnit\Framework\TestCase;
use Modules\Invoices\Infrastructure\Eloquent\Mapper\InvoiceMapper;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;

class InvoiceMapperTest extends TestCase
{
    public function testFromEloquent(): void
    {
        $eloquentInvoice = $this->createMock(InvoiceModel::class);
        $invoiceLineMock = $this->createMock(InvoiceProductLineModel::class);

        $invoiceLineMock->method('__get')->willReturnMap([
            ['id', Uuid::v4()->toRfc4122()],
            ['name', 'Bowcaster'],
            ['quantity', 2],
            ['unit_price', 100],
            ['total_unit_price', 200],
        ]);
        $eloquentInvoice->method('__get')->willReturnMap([
            ['id', Uuid::v4()->toRfc4122()],
            ['customer_name', 'Han Solo'],
            ['customer_email', 'han.solo@korelia.xyz'],
            ['status', StatusEnum::Draft],
            ['total_price', 400],
            ['invoiceLines', [$invoiceLineMock]],
        ]);

        $invoice = InvoiceMapper::fromEloquent($eloquentInvoice);

        // Assert Invoice object
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(new InvoiceId(Uuid::fromString($eloquentInvoice->id)), $invoice->id);
        $this->assertEquals(new CustomerInfo('Han Solo', 'han.solo@korelia.xyz'), $invoice->customerInfo);
        $this->assertTrue($invoice->getTotalPrice()->isEqual(new Money(400)));
        $this->assertEquals(StatusEnum::Draft, $invoice->getStatus());

        // Assert InvoiceProductLine object
        $this->assertCount(1, $invoice->getInvoiceProductLines());
        $productLine = $invoice->getInvoiceProductLines()[0];
        $this->assertInstanceOf(InvoiceProductLine::class, $productLine);
        $this->assertEquals('Bowcaster', $productLine->name);
        $this->assertEquals(2, $productLine->quantity);
        $this->assertTrue($productLine->price->isEqual(new Money(100)));
        $this->assertTrue($productLine->getTotalUnitPrice()->isEqual(new Money(200)));
    }
}
