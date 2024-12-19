<?php
declare (strict_types=1);

namespace Modules\Invoices\Api\Events;

use Modules\Invoices\Application\Dtos\InvoiceDto;

class InvoiceCreationRequestEvent
{
    public function __construct(
        public InvoiceDto $invoiceDto
    ) {}
}
