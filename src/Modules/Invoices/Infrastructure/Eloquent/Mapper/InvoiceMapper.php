<?php
declare (strict_types=1);

namespace Modules\Invoices\Infrastructure\Eloquent\Mapper;

use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Modules\Invoices\Infrastructure\Eloquent\Model\InvoiceModel;
use Shared\Domain\ValueObject\Money;
use Symfony\Component\Uid\Uuid;

class InvoiceMapper
{
    public static function fromEloquent(InvoiceModel $eloquentInvoice): Invoice
    {
        $customerInfo = new CustomerInfo(
            $eloquentInvoice->customer_name,
            $eloquentInvoice->customer_email
        );

        $invoiceLines = [];
        foreach ($eloquentInvoice->invoiceLines as $line) {
            $invoiceLines[] = InvoiceProductLine::recreate(
                id: new InvoiceProductLineId(Uuid::fromString($line->id)),
                name: $line->name,
                quantity: $line->quantity,
                price: new Money($line->unit_price),
                totalUnitPrice: new Money($line->total_unit_price)
            );
        }

        return Invoice::recreate(
            id: new InvoiceId(Uuid::fromString($eloquentInvoice->id)),
            customerInfo: $customerInfo,
            invoiceProductLines: $invoiceLines,
            status: $eloquentInvoice->status,
            totalPrice: new Money($eloquentInvoice->total_price)
        );
    }
}
