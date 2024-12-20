<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Domain\Entities;

use Modules\Invoices\Domain\Events\InvoiceCreatedEvent;
use Modules\Invoices\Domain\Events\InvoiceSendingEvent;
use Modules\Invoices\Domain\Events\InvoiceSentEvent;
use PHPUnit\Framework\TestCase;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Shared\Domain\ValueObject\Money;
use DomainException;
use Symfony\Component\Uid\Uuid;

class InvoiceTest extends TestCase
{
    private InvoiceId $invoiceId;
    private CustomerInfo $customerInfo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->invoiceId = new InvoiceId(Uuid::v7());
        $this->customerInfo = new CustomerInfo('Han Solo', 'han.solo@korelia.xyz');
    }

    public function testInvoiceCreation(): void
    {
        $invoice = Invoice::create($this->invoiceId, $this->customerInfo);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($this->invoiceId, $invoice->id);
        $this->assertTrue($this->customerInfo->isEqual($invoice->customerInfo));
        $this->assertEquals(StatusEnum::Draft, $invoice->getStatus());
    }

    public function testAddProductLine(): void
    {
        $invoice = Invoice::create($this->invoiceId, $this->customerInfo);

        $invoice->addInvoiceProductLine(
            productLineId: new InvoiceProductLineId(Uuid::v7()),
            name: 'Bowcaster',
            quantity: 1,
            unitPrice: new Money(100)
        );

        $invoice->addInvoiceProductLine(
            productLineId: new InvoiceProductLineId(Uuid::v7()),
            name: 'Bowcaster',
            quantity: 3,
            unitPrice: new Money(50)
        );

        $this->assertCount(2, $invoice->getInvoiceProductLines());
        $this->assertTrue($invoice->getTotalPrice()->isEqual(new Money(250)));

        $productLine = $invoice->getInvoiceProductLines()[0];
        $this->assertEquals('Bowcaster', $productLine->name);
        $this->assertEquals(1, $productLine->quantity);
        $this->assertTrue($productLine->price->isEqual(new Money(100)));
    }

    public function testSendInvoice(): void
    {
        $invoice = Invoice::create($this->invoiceId, $this->customerInfo);

        $this->assertEquals(StatusEnum::Draft, $invoice->getStatus());

        $invoice->send();

        $this->assertEquals(StatusEnum::Sending, $invoice->getStatus());
        $this->assertContains(InvoiceSendingEvent::class, array_map(fn($event) => $event::class, $invoice->pullEvents()));
    }

    public function testMarkInvoiceAsSent(): void
    {
        $invoice = Invoice::create($this->invoiceId, $this->customerInfo);
        $invoice->send();

        $invoice->markAsSent();

        $this->assertEquals(StatusEnum::SentToClient, $invoice->getStatus());
        $this->assertContains(InvoiceSentEvent::class, array_map(fn($event) => $event::class, $invoice->pullEvents()));
    }

    public function testCannotSendInvoiceIfNotInDraft(): void
    {
        $invoice = Invoice::create($this->invoiceId, $this->customerInfo);
        $invoice->send();

        $this->expectException(DomainException::class);
        $invoice->send();
    }

    public function testCannotMarkAsSentIfNotSending(): void
    {
        $invoice = Invoice::create($this->invoiceId, $this->customerInfo);

        $this->expectException(DomainException::class);
        $invoice->markAsSent();
    }
}
