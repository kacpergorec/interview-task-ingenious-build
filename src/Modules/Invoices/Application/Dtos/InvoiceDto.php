<?php
declare (strict_types=1);

namespace Modules\Invoices\Application\Dtos;

use Illuminate\Http\Request;
use Modules\Invoices\Domain\ValueObjects\CustomerInfo;
use Modules\Invoices\Domain\ValueObjects\InvoiceId;
use Modules\Invoices\Domain\ValueObjects\InvoiceProductLineId;

class InvoiceDto
{
    public function __construct(
        public InvoiceId $id,
        public CustomerInfo $customerInfo,
        /** @var InvoiceProductLineDto[] */
        public array $invoiceLines = []
    ) {}
}
