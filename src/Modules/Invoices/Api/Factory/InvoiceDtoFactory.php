<?php
declare (strict_types=1);

namespace Modules\Invoices\Api\Factory;

use Illuminate\Http\Request;
use Modules\Invoices\Application\Dtos\InvoiceDto;
use Modules\Invoices\Application\Dtos\InvoiceProductLineDto;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;
use Shared\Domain\ValueObject\Money;

readonly class InvoiceDtoFactory
{
    public static function fromRequest(Request $request): InvoiceDto
    {
        return new InvoiceDto(
            InvoiceId::new(),
            new CustomerInfo(
                $request->input('customerInfo.name'),
                $request->input('customerInfo.email')
            ),
            array_map(
                fn($line) => new InvoiceProductLineDto(
                    id: InvoiceProductLineId::new(),
                    quantity: $line['quantity'],
                    name: $line['name'],
                    price: new Money($line['price'])
                ),
                $request->input('invoiceProductLines', [])
            )
        );
    }
}
