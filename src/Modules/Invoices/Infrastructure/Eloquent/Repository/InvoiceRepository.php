<?php
declare (strict_types=1);

namespace Modules\Invoices\Infrastructure\Eloquent\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void
    {
        DB::beginTransaction();

        try {
            $eloquentInvoice = InvoiceModel::create([
                'id' => $invoice->id,
                'customer_name' => $invoice->customerInfo->name,
                'customer_email' => $invoice->customerInfo->email,
                'status' => $invoice->getStatus(),
                'total_price' => $invoice->getTotalPrice()->value,
            ]);

            foreach ($invoice->getInvoiceProductLines() as $line) {
                $eloquentInvoice->invoiceLines()->create([
                    'id' => $line->id,
                    'name' => $line->name,
                    'quantity' => $line->quantity,
                    'unit_price' => $line->price->value,
                    'total_unit_price' => $line->getTotalUnitPrice()->value,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Invoice saving failed', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }
}
