<?php
declare(strict_types=1);

namespace Tests\Unit\Invoices\Infrastructure\Eloquent\Repository;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceRepository;
use Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;
use Tests\TestCase;

class InvoiceRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testSaveInvoice(): void
    {
        $invoiceId = new InvoiceId(Uuid::v7());
        $customerInfo = new CustomerInfo('Han Solo', 'han.solo@korelia.xyz');

        $invoice = Invoice::create($invoiceId, $customerInfo);
        $invoice->addInvoiceProductLine(
            productLineId: new InvoiceProductLineId(Uuid::v7()),
            name: 'Bowcaster',
            quantity: 2,
            unitPrice: new Money(100)
        );

        $repository = new InvoiceRepository();
        $repository->save($invoice);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceId,
            'customer_name' => 'Han Solo',
            'customer_email' => 'han.solo@korelia.xyz',
            'status' => 'draft',
            'total_price' => 200,
        ]);

        $this->assertDatabaseHas('invoice_product_lines', [
            'invoice_id' => $invoiceId,
            'name' => 'Bowcaster',
            'quantity' => 2,
            'unit_price' => 100,
            'total_unit_price' => 200,
        ]);
    }
}
