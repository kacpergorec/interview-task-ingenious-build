<?php
declare (strict_types=1);

namespace Modules\Invoices\Infrastructure\Eloquent\Repository;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;

class InvoiceRepository extends EloquentRepository implements InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void
    {
        $eloquentInvoice = InvoiceModel::create(
            ['id' => $invoice->id],
            [
                'customer_name' => $invoice->customerInfo->name,
                'customer_email' => $invoice->customerInfo->email,
                'status' => $invoice->getStatus(),
                'total_price' => $invoice->getTotalPrice(),
            ]
        );

        foreach ($invoice->getInvoiceProductLines() as $line) {
            $eloquentInvoice->invoiceLines()->create([
                'id' => $line->id,
                'product_name' => $line->name,
                'quantity' => $line->quantity,
                'unit_price' => $line->price,
                'total_unit_price' => $line->getTotalUnitPrice()
            ]);
        }
    }
}
