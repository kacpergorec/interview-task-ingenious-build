<?php
declare (strict_types=1);

namespace Tests\Unit\Invoices\Infrastructure\Eloquent\Repository;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Modules\Invoices\Infrastructure\Eloquent\Repository\InvoiceQueryRepository;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;
use Symfony\Component\Uid\Uuid;
use Tests\TestCase;

class InvoiceQueryRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testFindInvoice(): void
    {
        $invoiceId = InvoiceId::new();
        $invoiceLineId = new InvoiceProductLineId(Uuid::v7());

        $invoiceModel = InvoiceModel::create([
            'id' => $invoiceId,
            'customer_name' => 'Han Solo',
            'customer_email' => 'han.solo@korelia.xyz',
            'status' => 'draft',
            'total_price' => 400,
        ]);

        $invoiceModel->invoiceLines()->create([
            'id' => $invoiceLineId,
            'name' => 'Bowcaster',
            'quantity' => 2,
            'unit_price' => 100,
            'total_unit_price' => 200,
        ]);

        $repository = new InvoiceQueryRepository();
        $invoice = $repository->find($invoiceId);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertTrue($invoiceId->toUuid()->equals($invoice->id->toUuid()));
    }

    public function testFindInvoiceNotFound(): void
    {
        $invoiceId = InvoiceId::new();

        $repository = new InvoiceQueryRepository();
        $invoice = $repository->find($invoiceId);

        $this->assertNull($invoice);
    }
}
